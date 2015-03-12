<?php
/**
 * Created by PhpStorm.
 * User: cachorios
 * Date: 31/07/14
 * Time: 19:15
 */

namespace IPSM\UtilidadBundle\Libs;


class DBFManager {
    var $dbf_num_rec; //Number of records in the file
    var $dbf_num_field; //Number of columns in each row
    var $dbf_names = array(); //Information on each column ['name'],['len'],['type']
    var $dbf_index = array(); //Array asociativo para acceso rapido al id de la columna by Lar
    //These are private....
    var $_raw; //The raw input file
    var $_rowsize; //Length of each row
    var $_hdrsize; //Length of the header information (offset to 1st record)
    var $_memos; //The raw memo file (if there is one).

    function __construct($filename)
    {
        if (!file_exists($filename)) {
            throw new \Exception('Not a valid DBF file !!!');
        }
        $tail = substr($filename, -4);
        if (strcasecmp($tail, '.dbf') != 0) {
            throw new \Exception('Not a valid DBF file !!!');
        }

        //Read the File
        $handle = fopen($filename, "r");
        if (!$handle) {
            throw new \Exception('Cannot read DBF file!!!');
        }

        $filesize = filesize($filename);

        $this->_raw = fread($handle, $filesize);
        fclose($handle);
        //Make sure that we indeed have a dbf file...
        if (!(ord($this->_raw[0]) == 3 || ord($this->_raw[0]) == 131) && ord($this->_raw[$filesize]) != 26) {
            throw new \Exception('Not a valid DBF file !!!');
        }
        // 3= file without DBT memo file; 131 ($83)= file with a DBT.
        $arrHeaderHex = array();
        for ($i = 0; $i < 32; $i++) {
            $arrHeaderHex[$i] = str_pad(dechex(ord($this->_raw[$i])), 2, "0", STR_PAD_LEFT);
        }
        //Initial information
        $line = 32; //Header Size

        //Number of records
        $this->dbf_num_rec = hexdec($arrHeaderHex[7] . $arrHeaderHex[6] . $arrHeaderHex[5] . $arrHeaderHex[4]);
        $this->_hdrsize = hexdec($arrHeaderHex[9] . $arrHeaderHex[8]); //Header Size+Field Descriptor

        //Number of fields
        $this->_rowsize = hexdec($arrHeaderHex[11] . $arrHeaderHex[10]);
        $this->dbf_num_field = floor(($this->_hdrsize - $line) / $line); //Number of Fields

        //Field properties retrieval looping
        for ($j = 0; $j < $this->dbf_num_field; $j++) {
            $name = '';
            $beg = $j * $line + $line;
            for ($k = $beg; $k < $beg + 11; $k++) {
                if (ord($this->_raw[$k]) > 30 && ord($this->_raw[$k]) < 123) {
                    $name .= $this->_raw[$k];
                } else {
                    break;
                }
            }
            $this->dbf_index[$name] = $j; //By lar para el indice

            $this->dbf_names[$j]['name'] = $name; //Name of the Field
            $this->dbf_names[$j]['len'] = ord($this->_raw[$beg + 16]); //Length of the field
            $this->dbf_names[$j]['type'] = $this->_raw[$beg + 11];
        }
        if (ord($this->_raw[0]) == 131) { //See if this has a memo file with it...
            //Read the File
            $tail = substr($tail, -1, 1); //Get the last character...
            if ($tail == 'F') { //See if upper or lower case
                $tail = 'T'; //Keep the case the same
            } else {
                $tail = 't';
            }
            $memoname = substr($filename, 0, strlen($filename) - 1) . $tail;
            $handle = fopen($memoname, "r");
            if (!$handle) {
                throw new \Exception('Cannot read DBT file!!!');
            }
            $filesize = filesize($memoname);
            $this->_memos = fread($handle, $filesize);
            fclose($handle);
        }
    }

    /**
     * getIndexField
     * @return array
     */
    function getIndexFields(){
        return $this->dbf_index;
    }

    function getRow($recnum)
    {
        $memoeot = chr(26) . chr(26);
        $rawrow = substr($this->_raw, $recnum * $this->_rowsize + $this->_hdrsize, $this->_rowsize);
        $rowrecs = array();
        $beg = 1;
        if (ord($rawrow[0]) == 42) {
            return false; //Record is deleted...
        }
        for ($i = 0; $i < $this->dbf_num_field; $i++) {
            $col = trim(substr($rawrow, $beg, $this->dbf_names[$i]['len']));
            if ($this->dbf_names[$i]['type'] != 'M') {
                $rowrecs[] = $col;
            } else {
                $memobeg = $col * 512; //Find start of the memo block (0=header so it works)
                $memoend = strpos($this->_memos, $memoeot, $memobeg); //Find the end of the memo
                $rowrecs[] = substr($this->_memos, $memobeg, $memoend - $memobeg);
            }
            $beg += $this->dbf_names[$i]['len'];
        }
        return $rowrecs;
    }

    function getRowAssoc($recnum)
    {
        $rawrow = substr($this->_raw, $recnum * $this->_rowsize + $this->_hdrsize, $this->_rowsize);
        $memoeot = chr(26) . chr(26);

        $rowrecs = array();
        $beg = 1;
        if (ord($rawrow[0]) == 42) {
            return false; //Record is deleted...
        }
        for ($i = 0; $i < $this->dbf_num_field; $i++) {
            $col = trim(substr($rawrow, $beg, $this->dbf_names[$i]['len']));
            if ($this->dbf_names[$i]['type'] != 'M') {
                $rowrecs[$this->dbf_names[$i]['name']] = $col;
            } else {
                $memobeg = $col * 512; //Find start of the memo block (0=header so it works)
                $memoend = strpos($this->_memos, $memoeot, $memobeg); //Find the end of the memo
                $rowrecs[$this->dbf_names[$i]['name']] = substr($this->_memos, $memobeg, $memoend - $memobeg);
            }
            $beg += $this->dbf_names[$i]['len'];
        }
        return $rowrecs;
    }
} 