<?php
// 	This file is part of Diacamma, a software developped by "Le Sanglier du Libre" (http://www.sd-libre.fr)
// 	Thanks to have payed a retribution for using this module.
// 
// 	Diacamma is free software; you can redistribute it and/or modify
// 	it under the terms of the GNU General Public License as published by
// 	the Free Software Foundation; either version 2 of the License, or
// 	(at your option) any later version.
// 
// 	Diacamma is distributed in the hope that it will be useful,
// 	but WITHOUT ANY WARRANTY; without even the implied warranty of
// 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// 	GNU General Public License for more details.
// 
// 	You should have received a copy of the GNU General Public License
// 	along with Lucterios; if not, write to the Free Software
// 	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
// 
// 		Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY
// library file write by SDK tool
// --- Last modification: Date 07 March 2011 23:50:45 By  ---

//@BEGIN@
define ('ARCHIVE_TAR_ATT_SEPARATOR', 90001);
define ('ARCHIVE_TAR_END_BLOCK', pack("a512", ''));

class ArchiveTar
{
    /**
    * @var string Name of the Tar
    */
    private $mTarname='';

    /**
    * @var boolean if true, the Tar file will be gzipped
    */
    private $mCompress=false;

    /**
    * @var string Explode separator
    */
    private $mSeparator=' ';

    /**
    * @var file descriptor
    */
    private $mFile=0;

    /**
    * @var string Local Tar name of a remote Tar (http:// or ftp://)
    */
    private $_temp_tarname='';

    public function __construct($pTarname, $pCompress = false)
    {
        $this->mCompress = false;
        if (!$pCompress) {
            if (@file_exists($pTarname)) {
                if ($fp = @fopen($pTarname, "rb")) {
                    // look for gzip magic cookie
                    $data = fread($fp, 2);
                    fclose($fp);
                    if ($data == "\37\213") {
                        $this->mCompress = true;
                    }
                }
            } else {
                if (substr($pTarname, -2) == 'gz') {
                    $this->mCompress = true;
                }
            }
        } else {
            $this->mCompress = true;
        }
        $this->mTarname = $pTarname;
        if ($this->mCompress) { // assert zlib or bz2 extension support
            if (!extension_loaded('zlib')) {
                $this->_error('zlib library unknown!');
		return false;
            }
        }
    }
    // }}}

    // {{{ destructor
    public function __destruct()
    {
        $this->_close();
        // ----- Look for a local copy to delete
        if ($this->_temp_tarname != '')
            @unlink($this->_temp_tarname);
    }

    public function create($p_filelist)
    {
        return $this->createModify($p_filelist, '', '');
    }
    // }}}

    // {{{ add()
    public function add($p_filelist)
    {
        return $this->addModify($p_filelist, '', '');
    }
    // }}}

    // {{{ extract()
    public function extract($p_path='')
    {
        return $this->extractModify($p_path, '');
    }
    // }}}

    // {{{ listContent()
    public function listContent()
    {
        $v_list_detail = array();

        if ($this->_openRead()) {
            if (!$this->_extractList('', $v_list_detail, "list", '', '')) {
                unset($v_list_detail);
                $v_list_detail = 0;
            }
            $this->_close();
        }

        return $v_list_detail;
    }
    // }}}

    // {{{ createModify()
    public function createModify($p_filelist, $p_add_dir, $p_remove_dir='')
    {
        $v_result = true;

        if (!$this->_openWrite())
            return false;

        if ($p_filelist != '') {
            if (is_array($p_filelist))
                $v_list = $p_filelist;
            elseif (is_string($p_filelist))
                $v_list = explode($this->mSeparator, $p_filelist);
            else {
                $this->_cleanFile();
                $this->_error('Invalid file list');
                return false;
            }

            $v_result = $this->_addList($v_list, $p_add_dir, $p_remove_dir);
        }

        if ($v_result) {
            $this->_writeFooter();
            $this->_close();
        } else
            $this->_cleanFile();

        return $v_result;
    }
    // }}}

    // {{{ addModify()
    public function addModify($p_filelist, $p_add_dir, $p_remove_dir='')
    {
        $v_result = true;

        if (!$this->_isArchive())
            $v_result = $this->createModify($p_filelist, $p_add_dir,
			                                $p_remove_dir);
        else {
            if (is_array($p_filelist))
                $v_list = $p_filelist;
            elseif (is_string($p_filelist))
                $v_list = explode($this->mSeparator, $p_filelist);
            else {
                $this->_error('Invalid file list');
                return false;
            }

            $v_result = $this->_append($v_list, $p_add_dir, $p_remove_dir);
        }

        return $v_result;
    }
    // }}}

    // {{{ addString()
    public function addString($p_filename, $p_string)
    {
        $v_result = true;

        if (!$this->_isArchive()) {
            if (!$this->_openWrite()) {
                return false;
            }
            $this->_close();
        }

        if (!$this->_openAppend())
            return false;

        // Need to check the get back to the temporary file ? ....
        $v_result = $this->_addString($p_filename, $p_string);

        $this->_writeFooter();

        $this->_close();

        return $v_result;
    }
    // }}}

    // {{{ extractModify()
    public function extractModify($p_path, $p_remove_path)
    {
        $v_result = true;
        $v_list_detail = array();

        if ($v_result = $this->_openRead()) {
            $v_result = $this->_extractList($p_path, $v_list_detail,
			                                "complete", 0, $p_remove_path);
            $this->_close();
        }

        return $v_result;
    }
    // }}}

    // {{{ extractInString()
    public function extractInString($p_filename)
    {
        if ($this->_openRead()) {
            $v_result = $this->_extractInString($p_filename);
            $this->_close();
        } else {
            $v_result = NULL;
        }

        return $v_result;
    }
    // }}}

    // {{{ extractList()
    public function extractList($p_filelist, $p_path='', $p_remove_path='')
    {
        $v_result = true;
        $v_list_detail = array();

        if (is_array($p_filelist))
            $v_list = $p_filelist;
        elseif (is_string($p_filelist))
            $v_list = explode($this->mSeparator, $p_filelist);
        else {
            $this->_error('Invalid string list');
            return false;
        }

        if ($v_result = $this->_openRead()) {
            $v_result = $this->_extractList($p_path, $v_list_detail, "partial",
			                                $v_list, $p_remove_path);
            $this->_close();
        }

        return $v_result;
    }
    // }}}

    // {{{ setAttribute()
    public function setAttribute()
    {
        $v_result = true;

        // ----- Get the number of variable list of arguments
        if (($v_size = func_num_args()) == 0) {
            return true;
        }

        // ----- Get the arguments
        $v_att_list = &func_get_args();

        // ----- Read the attributes
        $i=0;
        while ($i<$v_size) {

            // ----- Look for next option
            switch ($v_att_list[$i]) {
                // ----- Look for options that request a string value
                case ARCHIVE_TAR_ATT_SEPARATOR :
                    // ----- Check the number of parameters
                    if (($i+1) >= $v_size) {
                        $this->_error("Invalid number of parameters for attribute ARCHIVE_TAR_ATT_SEPARATOR");
                        return false;
                    }

                    // ----- Get the value
                    $this->mSeparator = $v_att_list[$i+1];
                    $i++;
                break;

                default :
                    $this->_error("Unknow attribute code '".$v_att_list[$i]."'");
                    return false;
            }

            // ----- Next attribute
            $i++;
        }

        return $v_result;
    }
    // }}}

    // {{{ _error()
    private function _error($p_message)
    {
	require_once("CORE/Lucterios_Error.inc.php");
	throw new LucteriosException(CRITIC,$p_message);
    }
    // }}}

    // {{{ _warning()
    private function _warning($p_message)
    {
        // ----- To be completed
	require_once("CORE/Lucterios_Error.inc.php");
        throw new LucteriosException(IMPORTANT,$p_message);
    }
    // }}}

    // {{{ _isArchive()
    private function _isArchive($p_filename=NULL)
    {
        if ($p_filename == NULL) {
            $p_filename = $this->mTarname;
        }
        clearstatcache();
        return @is_file($p_filename);
    }
    // }}}

    // {{{ _openWrite()
    private function _openWrite()
    {
        if ($this->mCompress)
            $this->mFile = @gzopen($this->mTarname, "wb9");
        else
            $this->mFile = @fopen($this->mTarname, "wb");
        if ($this->mFile == 0) {
            $this->_error("Unable to open in write mode '".$this->mTarname."'");
            return false;
        }

        return true;
    }
    // }}}

    // {{{ _openRead()
    private function _openRead()
    {
        if (strtolower(substr($this->mTarname, 0, 7)) == 'http://') {

          // ----- Look if a local copy need to be done
          if ($this->_temp_tarname == '') {
              $this->_temp_tarname = uniqid('tar').'.tmp';
              if (!$v_file_from = @fopen($this->mTarname, 'rb')) {
                $this->_error("Unable to open in read mode '".$this->mTarname."'");
                $this->_temp_tarname = '';
                return false;
              }
              if (!$v_file_to = @fopen($this->_temp_tarname, 'wb')) {
                $this->_error("Unable to open in write mode '".$this->mTarname."'");
                $this->_temp_tarname = '';
                return false;
              }
              while ($v_data = @fread($v_file_from, 1024))
                  @fwrite($v_file_to, $v_data);
              @fclose($v_file_from);
              @fclose($v_file_to);
          }

          // ----- File to open if the local copy
          $v_filename = $this->_temp_tarname;

        } else
          // ----- File to open if the normal Tar file
          $v_filename = $this->mTarname;

        if ($this->mCompress)
            $this->mFile = @gzopen($v_filename, "rb");
        else
	    $this->mFile = @fopen($v_filename, "rb");
        if ($this->mFile == 0) {
            $this->_error("Unable to open in read mode '".$v_filename."'");
            return false;
        }

        return true;
    }
    // }}}

    // {{{ _openReadWrite()
    private function _openReadWrite()
    {
        if ($this->mCompress)
            $this->mFile = @gzopen($this->mTarname, "r+b");
        else
	    $this->mFile = @fopen($this->mTarname, "r+b");

        if ($this->mFile == 0) {
            $this->_error("Unable to open in read/write mode '".$this->mTarname."'");
            return false;
        }

        return true;
    }
    // }}}

    // {{{ _close()
    private function _close()
    {
        //if (isset($this->mFile)) {
        if (is_resource($this->mFile)) {
            if ($this->mCompress)
                @gzclose($this->mFile);
            else
                @fclose($this->mFile);

            $this->mFile = 0;
        }

        // ----- Look if a local copy need to be erase
        // Note that it might be interesting to keep the url for a time : ToDo
        if ($this->_temp_tarname != '') {
            @unlink($this->_temp_tarname);
            $this->_temp_tarname = '';
        }

        return true;
    }
    // }}}

    // {{{ _cleanFile()
    private function _cleanFile()
    {
        $this->_close();

        // ----- Look for a local copy
        if ($this->_temp_tarname != '') {
            // ----- Remove the local copy but not the remote tarname
            @unlink($this->_temp_tarname);
            $this->_temp_tarname = '';
        } else {
            // ----- Remove the local tarname file
            @unlink($this->mTarname);
        }
        $this->mTarname = '';

        return true;
    }
    // }}}

    // {{{ _writeBlock()
    private function _writeBlock($p_binary_data, $p_len=null)
    {
      if (is_resource($this->mFile)) {
          if ($p_len === null) {
              if ($this->mCompress)
                  @gzputs($this->mFile, $p_binary_data);
              else
                  @fputs($this->mFile, $p_binary_data);
          } else {
              if ($this->mCompress)
                  @gzputs($this->mFile, $p_binary_data, $p_len);
              else
                  @fputs($this->mFile, $p_binary_data, $p_len);
          }
      }
      return true;
    }
    // }}}

    // {{{ _readBlock()
    private function _readBlock()
    {
      $v_block = null;
      if (is_resource($this->mFile)) {
          if ($this->mCompress)
              $v_block = @gzread($this->mFile, 512);
          else
              $v_block = @fread($this->mFile, 512);
      }
      return $v_block;
    }
    // }}}

    // {{{ _jumpBlock()
    private function _jumpBlock($p_len=null)
    {
      if (is_resource($this->mFile)) {
          if ($p_len === null)
              $p_len = 1;

          if ($this->mCompress) {
              @gzseek($this->mFile, gztell($this->mFile)+($p_len*512));
          }
          else
              @fseek($this->mFile, ftell($this->mFile)+($p_len*512));

      }
      return true;
    }
    // }}}

    // {{{ _writeFooter()
    private function _writeFooter()
    {
      if (is_resource($this->mFile)) {
          // ----- Write the last 0 filled block for end of archive
          $v_binary_data = pack('a1024', '');
          $this->_writeBlock($v_binary_data);
      }
      return true;
    }
    // }}}

    // {{{ _addList()
    private function _addList($p_list, $p_add_dir, $p_remove_dir)
    {
      $v_result=true;
      $v_header = array();

      // ----- Remove potential windows directory separator
      $p_add_dir = $this->_translateWinPath($p_add_dir);
      $p_remove_dir = $this->_translateWinPath($p_remove_dir, false);

      if (!$this->mFile) {
          $this->_error('Invalid file descriptor');
          return false;
      }

      if (sizeof($p_list) == 0)
          return true;

      foreach ($p_list as $v_filename) {
          if (!$v_result) {
              break;
          }

        // ----- Skip the current tar name
        if ($v_filename == $this->mTarname)
            continue;

        if ($v_filename == '')
            continue;

        if (!file_exists($v_filename)) {
            $this->_warning("File '$v_filename' does not exist");
            continue;
        }

        // ----- Add the file or directory header
        if (!$this->_addFile($v_filename, $v_header, $p_add_dir, $p_remove_dir))
            return false;

        if (@is_dir($v_filename)) {
            if (!($p_hdir = opendir($v_filename))) {
                $this->_warning("Directory '$v_filename' can not be read");
                continue;
            }
            while (false !== ($p_hitem = readdir($p_hdir))) {
                if ($p_hitem[0] != '.') { // Ignore hidden file
                    if ($v_filename != ".")
                        $p_temp_list[0] = $v_filename.'/'.$p_hitem;
                    else
                        $p_temp_list[0] = $p_hitem;
                    $v_result = $this->_addList($p_temp_list,$p_add_dir,$p_remove_dir);
                }
            }

            unset($p_temp_list);
            unset($p_hdir);
            unset($p_hitem);
        }
      }

      return $v_result;
    }
    // }}}

    // {{{ _addFile()
    private function _addFile($p_filename, &$p_header, $p_add_dir, $p_remove_dir)
    {
      if (!$this->mFile) {
          $this->_error('Invalid file descriptor');
          return false;
      }

      if ($p_filename == '') {
          $this->_error('Invalid file name');
          return false;
      }

      // ----- Calculate the stored filename
      $p_filename = $this->_translateWinPath($p_filename, false);;
      $v_stored_filename = $p_filename;
      if (strcmp($p_filename, $p_remove_dir) == 0) {
          return true;
      }
      if ($p_remove_dir != '') {
          if (substr($p_remove_dir, -1) != '/')
              $p_remove_dir .= '/';

          if (substr($p_filename, 0, strlen($p_remove_dir)) == $p_remove_dir)
              $v_stored_filename = substr($p_filename, strlen($p_remove_dir));
      }
      $v_stored_filename = $this->_translateWinPath($v_stored_filename);
      if ($p_add_dir != '') {
          if (substr($p_add_dir, -1) == '/')
              $v_stored_filename = $p_add_dir.$v_stored_filename;
          else
              $v_stored_filename = $p_add_dir.'/'.$v_stored_filename;
      }

      $v_stored_filename = $this->_pathReduction($v_stored_filename);

      if ($this->_isArchive($p_filename)) {
          if (($v_file = @fopen($p_filename, "rb")) == 0) {
              $this->_warning("Unable to open file '".$p_filename
			                  ."' in binary read mode");
              return true;
          }

          if (!$this->_writeHeader($p_filename, $v_stored_filename))
              return false;

          while (($v_buffer = fread($v_file, 512)) != '') {
              $v_binary_data = pack("a512", "$v_buffer");
              $this->_writeBlock($v_binary_data);
          }

          fclose($v_file);

      } else {
          // ----- Only header for dir
          if (!$this->_writeHeader($p_filename, $v_stored_filename))
              return false;
      }

      return true;
    }
    // }}}

    // {{{ _addString()
    private function _addString($p_filename, $p_string)
    {
      if (!$this->mFile) {
          $this->_error('Invalid file descriptor');
          return false;
      }

      if ($p_filename == '') {
          $this->_error('Invalid file name');
          return false;
      }

      // ----- Calculate the stored filename
      $p_filename = $this->_translateWinPath($p_filename, false);;

      if (!$this->_writeHeaderBlock($p_filename, strlen($p_string),
	                                  time(), 384, "", 0, 0))
          return false;

      $i=0;
      while (($v_buffer = substr($p_string, (($i++)*512), 512)) != '') {
          $v_binary_data = pack("a512", $v_buffer);
          $this->_writeBlock($v_binary_data);
      }

      return true;
    }
    // }}}

    // {{{ _writeHeader()
    private function _writeHeader($p_filename, $p_stored_filename)
    {
        if ($p_stored_filename == '')
            $p_stored_filename = $p_filename;
        $v_reduce_filename = $this->_pathReduction($p_stored_filename);

        if (strlen($v_reduce_filename) > 99) {
          if (!$this->_writeLongHeader($v_reduce_filename))
            return false;
        }

        $v_info = stat($p_filename);
        $v_uid = sprintf("%6s ", DecOct($v_info[4]));
        $v_gid = sprintf("%6s ", DecOct($v_info[5]));
        $v_perms = sprintf("%6s ", DecOct(fileperms($p_filename)));

        $v_mtime = sprintf("%11s", DecOct(filemtime($p_filename)));

        if (@is_dir($p_filename)) {
          $v_typeflag = "5";
          $v_size = sprintf("%11s ", DecOct(0));
        } else {
          $v_typeflag = '';
          clearstatcache();
          $v_size = sprintf("%11s ", DecOct(filesize($p_filename)));
        }

        $v_linkname = '';

        $v_magic = '';

        $v_version = '';

        $v_uname = '';

        $v_gname = '';

        $v_devmajor = '';

        $v_devminor = '';

        $v_prefix = '';

        $v_binary_data_first = pack("a100a8a8a8a12A12",
		                            $v_reduce_filename, $v_perms, $v_uid,
									$v_gid, $v_size, $v_mtime);
        $v_binary_data_last = pack("a1a100a6a2a32a32a8a8a155a12",
		                           $v_typeflag, $v_linkname, $v_magic,
								   $v_version, $v_uname, $v_gname,
								   $v_devmajor, $v_devminor, $v_prefix, '');

        // ----- Calculate the checksum
        $v_checksum = 0;
        // ..... First part of the header
        for ($i=0; $i<148; $i++)
            $v_checksum += ord(substr($v_binary_data_first,$i,1));
        // ..... Ignore the checksum value and replace it by ' ' (space)
        for ($i=148; $i<156; $i++)
            $v_checksum += ord(' ');
        // ..... Last part of the header
        for ($i=156, $j=0; $i<512; $i++, $j++)
            $v_checksum += ord(substr($v_binary_data_last,$j,1));

        // ----- Write the first 148 bytes of the header in the archive
        $this->_writeBlock($v_binary_data_first, 148);

        // ----- Write the calculated checksum
        $v_checksum = sprintf("%6s ", DecOct($v_checksum));
        $v_binary_data = pack("a8", $v_checksum);
        $this->_writeBlock($v_binary_data, 8);

        // ----- Write the last 356 bytes of the header in the archive
        $this->_writeBlock($v_binary_data_last, 356);

        return true;
    }
    // }}}

    // {{{ _writeHeaderBlock()
    private function _writeHeaderBlock($p_filename, $p_size, $p_mtime=0, $p_perms=0,
	                           $p_type='', $p_uid=0, $p_gid=0)
    {
        $p_filename = $this->_pathReduction($p_filename);

        if (strlen($p_filename) > 99) {
          if (!$this->_writeLongHeader($p_filename))
            return false;
        }

        if ($p_type == "5") {
          $v_size = sprintf("%11s ", DecOct(0));
        } else {
          $v_size = sprintf("%11s ", DecOct($p_size));
        }

        $v_uid = sprintf("%6s ", DecOct($p_uid));
        $v_gid = sprintf("%6s ", DecOct($p_gid));
        $v_perms = sprintf("%6s ", DecOct($p_perms));

        $v_mtime = sprintf("%11s", DecOct($p_mtime));

        $v_linkname = '';

        $v_magic = '';

        $v_version = '';

        $v_uname = '';

        $v_gname = '';

        $v_devmajor = '';

        $v_devminor = '';

        $v_prefix = '';

        $v_binary_data_first = pack("a100a8a8a8a12A12",
		                            $p_filename, $v_perms, $v_uid, $v_gid,
									$v_size, $v_mtime);
        $v_binary_data_last = pack("a1a100a6a2a32a32a8a8a155a12",
		                           $p_type, $v_linkname, $v_magic,
								   $v_version, $v_uname, $v_gname,
								   $v_devmajor, $v_devminor, $v_prefix, '');

        // ----- Calculate the checksum
        $v_checksum = 0;
        // ..... First part of the header
        for ($i=0; $i<148; $i++)
            $v_checksum += ord(substr($v_binary_data_first,$i,1));
        // ..... Ignore the checksum value and replace it by ' ' (space)
        for ($i=148; $i<156; $i++)
            $v_checksum += ord(' ');
        // ..... Last part of the header
        for ($i=156, $j=0; $i<512; $i++, $j++)
            $v_checksum += ord(substr($v_binary_data_last,$j,1));

        // ----- Write the first 148 bytes of the header in the archive
        $this->_writeBlock($v_binary_data_first, 148);

        // ----- Write the calculated checksum
        $v_checksum = sprintf("%6s ", DecOct($v_checksum));
        $v_binary_data = pack("a8", $v_checksum);
        $this->_writeBlock($v_binary_data, 8);

        // ----- Write the last 356 bytes of the header in the archive
        $this->_writeBlock($v_binary_data_last, 356);

        return true;
    }
    // }}}

    // {{{ _writeLongHeader()
    private function _writeLongHeader($p_filename)
    {
        $v_size = sprintf("%11s ", DecOct(strlen($p_filename)));

        $v_typeflag = 'L';

        $v_linkname = '';

        $v_magic = '';

        $v_version = '';

        $v_uname = '';

        $v_gname = '';

        $v_devmajor = '';

        $v_devminor = '';

        $v_prefix = '';

        $v_binary_data_first = pack("a100a8a8a8a12A12",
		                            '././@LongLink', 0, 0, 0, $v_size, 0);
        $v_binary_data_last = pack("a1a100a6a2a32a32a8a8a155a12",
		                           $v_typeflag, $v_linkname, $v_magic,
								   $v_version, $v_uname, $v_gname,
								   $v_devmajor, $v_devminor, $v_prefix, '');

        // ----- Calculate the checksum
        $v_checksum = 0;
        // ..... First part of the header
        for ($i=0; $i<148; $i++)
            $v_checksum += ord(substr($v_binary_data_first,$i,1));
        // ..... Ignore the checksum value and replace it by ' ' (space)
        for ($i=148; $i<156; $i++)
            $v_checksum += ord(' ');
        // ..... Last part of the header
        for ($i=156, $j=0; $i<512; $i++, $j++)
            $v_checksum += ord(substr($v_binary_data_last,$j,1));

        // ----- Write the first 148 bytes of the header in the archive
        $this->_writeBlock($v_binary_data_first, 148);

        // ----- Write the calculated checksum
        $v_checksum = sprintf("%6s ", DecOct($v_checksum));
        $v_binary_data = pack("a8", $v_checksum);
        $this->_writeBlock($v_binary_data, 8);

        // ----- Write the last 356 bytes of the header in the archive
        $this->_writeBlock($v_binary_data_last, 356);

        // ----- Write the filename as content of the block
        $i=0;
        while (($v_buffer = substr($p_filename, (($i++)*512), 512)) != '') {
            $v_binary_data = pack("a512", "$v_buffer");
            $this->_writeBlock($v_binary_data);
        }

        return true;
    }
    // }}}

    // {{{ _readHeader()
    private function _readHeader($v_binary_data, &$v_header)
    {
        if (strlen($v_binary_data)==0) {
            $v_header['filename'] = '';
            return true;
        }

        if (strlen($v_binary_data) != 512) {
            $v_header['filename'] = '';
            $this->_error('Invalid block size : '.strlen($v_binary_data));
            return false;
        }

        if (!is_array($v_header)) {
            $v_header = array();
        }
        // ----- Calculate the checksum
        $v_checksum = 0;
        // ..... First part of the header
        for ($i=0; $i<148; $i++)
            $v_checksum+=ord(substr($v_binary_data,$i,1));
        // ..... Ignore the checksum value and replace it by ' ' (space)
        for ($i=148; $i<156; $i++)
            $v_checksum += ord(' ');
        // ..... Last part of the header
        for ($i=156; $i<512; $i++)
           $v_checksum+=ord(substr($v_binary_data,$i,1));

        $v_data = unpack("a100filename/a8mode/a8uid/a8gid/a12size/a12mtime/"
		                 ."a8checksum/a1typeflag/a100link/a6magic/a2version/"
						 ."a32uname/a32gname/a8devmajor/a8devminor",
						 $v_binary_data);

        // ----- Extract the checksum
        $v_header['checksum'] = OctDec(trim($v_data['checksum']));
        if ($v_header['checksum'] != $v_checksum) {
            $v_header['filename'] = '';

            // ----- Look for last block (empty block)
            if (($v_checksum == 256) && ($v_header['checksum'] == 0))
                return true;

            $this->_error('Invalid checksum for file "'.$v_data['filename']
			              .'" : '.$v_checksum.' calculated, '
						  .$v_header['checksum'].' expected');
            return false;
        }

        // ----- Extract the properties
        $v_header['filename'] = trim($v_data['filename']);
        if ($this->_maliciousFilename($v_header['filename'])) {
            $this->_error('Malicious .tar detected, file "' . $v_header['filename'] .
                '" will not install in desired directory tree');
            return false;
        }
        $v_header['mode'] = OctDec(trim($v_data['mode']));
        $v_header['uid'] = OctDec(trim($v_data['uid']));
        $v_header['gid'] = OctDec(trim($v_data['gid']));
        $v_header['size'] = OctDec(trim($v_data['size']));
        $v_header['mtime'] = OctDec(trim($v_data['mtime']));
        if (($v_header['typeflag'] = $v_data['typeflag']) == "5") {
          $v_header['size'] = 0;
        }
        $v_header['link'] = trim($v_data['link']);
        /* ----- All these fields are removed form the header because
		they do not carry interesting info
        $v_header[magic] = trim($v_data[magic]);
        $v_header[version] = trim($v_data[version]);
        $v_header[uname] = trim($v_data[uname]);
        $v_header[gname] = trim($v_data[gname]);
        $v_header[devmajor] = trim($v_data[devmajor]);
        $v_header[devminor] = trim($v_data[devminor]);
        */

        return true;
    }
    // }}}

    // {{{ _maliciousFilename()
    private function _maliciousFilename($file)
    {
        if (strpos($file, '/../') !== false) {
            return true;
        }
        if (strpos($file, '../') === 0) {
            return true;
        }
        return false;
    }
    // }}}

    // {{{ _readLongHeader()
    private function _readLongHeader(&$v_header)
    {
      $v_filename = '';
      $n = floor($v_header['size']/512);
      for ($i=0; $i<$n; $i++) {
        $v_content = $this->_readBlock();
        $v_filename .= $v_content;
      }
      if (($v_header['size'] % 512) != 0) {
        $v_content = $this->_readBlock();
        $v_filename .= $v_content;
      }

      // ----- Read the next header
      $v_binary_data = $this->_readBlock();

      if (!$this->_readHeader($v_binary_data, $v_header))
        return false;

      $v_header['filename'] = $v_filename;
        if ($this->_maliciousFilename($v_filename)) {
            $this->_error('Malicious .tar detected, file "' . $v_filename .
                '" will not install in desired directory tree');
            return false;
      }

      return true;
    }
    // }}}

    // {{{ _extractInString()
    private function _extractInString($p_filename)
    {
        $v_result_str = "";

        While (strlen($v_binary_data = $this->_readBlock()) != 0)
        {
          if (!$this->_readHeader($v_binary_data, $v_header))
            return NULL;

          if ($v_header['filename'] == '')
            continue;

          // ----- Look for long filename
          if ($v_header['typeflag'] == 'L') {
            if (!$this->_readLongHeader($v_header))
              return NULL;
          }

          if ($v_header['filename'] == $p_filename) {
              if ($v_header['typeflag'] == "5") {
                  $this->_error('Unable to extract in string a directory '
				                .'entry {'.$v_header['filename'].'}');
                  return NULL;
              } else {
                  $n = floor($v_header['size']/512);
                  for ($i=0; $i<$n; $i++) {
                      $v_result_str .= $this->_readBlock();
                  }
                  if (($v_header['size'] % 512) != 0) {
                      $v_content = $this->_readBlock();
                      $v_result_str .= substr($v_content, 0,
					                          ($v_header['size'] % 512));
                  }
                  return $v_result_str;
              }
          } else {
              $this->_jumpBlock(ceil(($v_header['size']/512)));
          }
        }

        return NULL;
    }
    // }}}

    // {{{ _extractList()
    private function _extractList($p_path, &$p_list_detail, $p_mode,
	                      $p_file_list, $p_remove_path)
    {
    $v_result=true;
    $v_nb = 0;
    $v_extract_all = true;
    $v_listing = false;

    $p_path = $this->_translateWinPath($p_path, false);
    if ($p_path == '' || (substr($p_path, 0, 1) != '/'
	    && substr($p_path, 0, 3) != "../" && !strpos($p_path, ':'))) {
      $p_path = "./".$p_path;
    }
    $p_remove_path = $this->_translateWinPath($p_remove_path);

    // ----- Look for path to remove format (should end by /)
    if (($p_remove_path != '') && (substr($p_remove_path, -1) != '/'))
      $p_remove_path .= '/';
    $p_remove_path_size = strlen($p_remove_path);

    switch ($p_mode) {
      case "complete" :
        $v_extract_all = TRUE;
        $v_listing = FALSE;
      break;
      case "partial" :
          $v_extract_all = FALSE;
          $v_listing = FALSE;
      break;
      case "list" :
          $v_extract_all = FALSE;
          $v_listing = TRUE;
      break;
      default :
        $this->_error('Invalid extract mode ('.$p_mode.')');
        return false;
    }

    clearstatcache();

    while (strlen($v_binary_data = $this->_readBlock()) != 0)
    {
      $v_extract_file = FALSE;
      $v_extraction_stopped = 0;

      if (!$this->_readHeader($v_binary_data, $v_header))
        return false;

      if ($v_header['filename'] == '') {
        continue;
      }

      // ----- Look for long filename
      if ($v_header['typeflag'] == 'L') {
        if (!$this->_readLongHeader($v_header))
          return false;
      }

      if ((!$v_extract_all) && (is_array($p_file_list))) {
        // ----- By default no unzip if the file is not found
        $v_extract_file = false;

        for ($i=0; $i<sizeof($p_file_list); $i++) {
          // ----- Look if it is a directory
          if (substr($p_file_list[$i], -1) == '/') {
            // ----- Look if the directory is in the filename path
            if ((strlen($v_header['filename']) > strlen($p_file_list[$i]))
			    && (substr($v_header['filename'], 0, strlen($p_file_list[$i]))
				    == $p_file_list[$i])) {
              $v_extract_file = TRUE;
              break;
            }
          }

          // ----- It is a file, so compare the file names
          elseif ($p_file_list[$i] == $v_header['filename']) {
            $v_extract_file = TRUE;
            break;
          }
        }
      } else {
        $v_extract_file = TRUE;
      }

      // ----- Look if this file need to be extracted
      if (($v_extract_file) && (!$v_listing))
      {
        if (($p_remove_path != '')
            && (substr($v_header['filename'], 0, $p_remove_path_size)
			    == $p_remove_path))
          $v_header['filename'] = substr($v_header['filename'],
		                                 $p_remove_path_size);
        if (($p_path != './') && ($p_path != '/')) {
          while (substr($p_path, -1) == '/')
            $p_path = substr($p_path, 0, strlen($p_path)-1);

          if (substr($v_header['filename'], 0, 1) == '/')
              $v_header['filename'] = $p_path.$v_header['filename'];
          else
            $v_header['filename'] = $p_path.'/'.$v_header['filename'];
        }
        if (file_exists($v_header['filename'])) {
          if (   (@is_dir($v_header['filename']))
		      && ($v_header['typeflag'] == '')) {
            $this->_error('File '.$v_header['filename']
			              .' already exists as a directory');
            return false;
          }
          if (   ($this->_isArchive($v_header['filename']))
		      && ($v_header['typeflag'] == "5")) {
            $this->_error('Directory '.$v_header['filename']
			              .' already exists as a file');
            return false;
          }
          if (!is_writeable($v_header['filename'])) {
            $this->_error('File '.$v_header['filename']
			              .' already exists and is write protected');
            return false;
          }
          if (filemtime($v_header['filename']) > $v_header['mtime']) {
            // To be completed : An error or silent no replace ?
          }
        }

        // ----- Check the directory availability and create it if necessary
        elseif (($v_result
		         = $this->_dirCheck(($v_header['typeflag'] == "5"
				                    ?$v_header['filename']
									:dirname($v_header['filename'])))) != 1) {
            $this->_error('Unable to create path for '.$v_header['filename']);
            return false;
        }

        if ($v_extract_file) {
          if ($v_header['typeflag'] == "5") {
            if (!@file_exists($v_header['filename'])) {
                if (!@mkdir($v_header['filename'], 0777)) {
                    $this->_error('Unable to create directory {'
					              .$v_header['filename'].'}');
                    return false;
                }
            }
          } elseif ($v_header['typeflag'] == "2") {
              if (!@symlink($v_header['link'], $v_header['filename'])) {
                  $this->_error('Unable to extract symbolic link {'
                                .$v_header['filename'].'}');
                  return false;
              }
          } else {
              if (($v_dest_file = @fopen($v_header['filename'], "wb")) == 0) {
                  $this->_error('Error while opening {'.$v_header['filename']
				                .'} in write binary mode');
                  return false;
              } else {
                  $n = floor($v_header['size']/512);
                  for ($i=0; $i<$n; $i++) {
                      $v_content = $this->_readBlock();
                      fwrite($v_dest_file, $v_content, 512);
                  }
            if (($v_header['size'] % 512) != 0) {
              $v_content = $this->_readBlock();
              fwrite($v_dest_file, $v_content, ($v_header['size'] % 512));
            }

            @fclose($v_dest_file);

            // ----- Change the file mode, mtime
            @touch($v_header['filename'], $v_header['mtime']);
            if ($v_header['mode'] & 0111) {
                // make file executable, obey umask
                $mode = fileperms($v_header['filename']) | (~umask() & 0111);
                @chmod($v_header['filename'], $mode);
            }
          }

          // ----- Check the file size
          clearstatcache();
          if (filesize($v_header['filename']) != $v_header['size']) {
              $this->_error("Extracted file '".$v_header['filename']."' does not have the correct file size '".filesize($v_header['filename'])."' (".$v_header['size']." expected). Archive may be corrupted.");
              return false;
          }
          }
        } else {
          $this->_jumpBlock(ceil(($v_header['size']/512)));
        }
      } else {
          $this->_jumpBlock(ceil(($v_header['size']/512)));
      }

      /* TBC : Seems to be unused ...
      if ($this->mCompress)
        $v_end_of_file = @gzeof($this->mFile);
      else
        $v_end_of_file = @feof($this->mFile);
        */

      if ($v_listing || $v_extract_file || $v_extraction_stopped) {
        // ----- Log extracted files
        if (($v_file_dir = dirname($v_header['filename']))
		    == $v_header['filename'])
          $v_file_dir = '';
        if ((substr($v_header['filename'], 0, 1) == '/') && ($v_file_dir == ''))
          $v_file_dir = '/';

        $p_list_detail[$v_nb++] = $v_header;
        if (is_array($p_file_list) && (count($p_list_detail) == count($p_file_list))) {
            return true;
        }
      }
    }

        return true;
    }
    // }}}

    // {{{ _openAppend()
    private function _openAppend()
    {
        if (filesize($this->mTarname) == 0)
          return $this->_openWrite();

        if ($this->mCompress) {
            $this->_close();

            if (!@rename($this->mTarname, $this->mTarname.".tmp")) {
                $this->_error("Error while renaming '".$this->mTarname."' to temporary file '".$this->mTarname.".tmp'");
                return false;
            }

            $v_temp_tar = @gzopen($this->mTarname.".tmp", "rb");
            if ($v_temp_tar == 0) {
                $this->_error("Unable to open file '".$this->mTarname.".tmp' in binary read mode");
                @rename($this->mTarname.".tmp", $this->mTarname);
                return false;
            }

            if (!$this->_openWrite()) {
                @rename($this->mTarname.".tmp", $this->mTarname);
                return false;
            }

	    while (!@gzeof($v_temp_tar)) {
		$v_buffer = @gzread($v_temp_tar, 512);
		if ($v_buffer == ARCHIVE_TAR_END_BLOCK) {
		    // do not copy end blocks, we will re-make them
		    // after appending
		    continue;
		}
		$v_binary_data = pack("a512", $v_buffer);
		$this->_writeBlock($v_binary_data);
	    }
	    @gzclose($v_temp_tar);
            if (!@unlink($this->mTarname.".tmp")) {
                $this->_error("Error while deleting temporary file '".$this->mTarname.".tmp'");
            }

        } else {
            // ----- For not compressed tar, just add files before the last
			//       one or two 512 bytes block
            if (!$this->_openReadWrite())
               return false;

            clearstatcache();
            $v_size = filesize($this->mTarname);

            // We might have zero, one or two end blocks.
            // The standard is two, but we should try to handle
            // other cases.
            fseek($this->mFile, $v_size - 1024);
            if (fread($this->mFile, 512) == ARCHIVE_TAR_END_BLOCK) {
                fseek($this->mFile, $v_size - 1024);
            }
            elseif (fread($this->mFile, 512) == ARCHIVE_TAR_END_BLOCK) {
                fseek($this->mFile, $v_size - 512);
            }
        }

        return true;
    }
    // }}}

    // {{{ _append()
    private function _append($p_filelist, $p_add_dir='', $p_remove_dir='')
    {
        if (!$this->_openAppend())
            return false;

        if ($this->_addList($p_filelist, $p_add_dir, $p_remove_dir))
           $this->_writeFooter();

        $this->_close();

        return true;
    }
    // }}}

    // {{{ _dirCheck()
    private function _dirCheck($p_dir)
    {
        clearstatcache();
        if ((@is_dir($p_dir)) || ($p_dir == ''))
            return true;

        $p_parent_dir = dirname($p_dir);

        if (($p_parent_dir != $p_dir) &&
            ($p_parent_dir != '') &&
            (!$this->_dirCheck($p_parent_dir)))
             return false;

        if (!@mkdir($p_dir, 0777)) {
            $this->_error("Unable to create directory '$p_dir'");
            return false;
        }

        return true;
    }

    // }}}

    // {{{ _pathReduction()
    private function _pathReduction($p_dir)
    {
        $v_result = '';

        // ----- Look for not empty path
        if ($p_dir != '') {
            // ----- Explode path by directory names
            $v_list = explode('/', $p_dir);

            // ----- Study directories from last to first
            for ($i=sizeof($v_list)-1; $i>=0; $i--) {
                // ----- Look for current path
                if ($v_list[$i] == ".") {
                    // ----- Ignore this directory
                    // Should be the first $i=0, but no check is done
                }
                else if ($v_list[$i] == "..") {
                    // ----- Ignore it and ignore the $i-1
                    $i--;
                }
                else if (   ($v_list[$i] == '')
				         && ($i!=(sizeof($v_list)-1))
						 && ($i!=0)) {
                    // ----- Ignore only the double '//' in path,
                    // but not the first and last /
                } else {
                    $v_result = $v_list[$i].($i!=(sizeof($v_list)-1)?'/'
					            .$v_result:'');
                }
            }
        }
        $v_result = strtr($v_result, '\\', '/');
        return $v_result;
    }

    // }}}

    // {{{ _translateWinPath()
    private function _translateWinPath($p_path, $p_remove_disk_letter=true)
    {
      if (defined('OS_WINDOWS') && OS_WINDOWS) {
          // ----- Look for potential disk letter
          if (   ($p_remove_disk_letter)
		      && (($v_position = strpos($p_path, ':')) != false)) {
              $p_path = substr($p_path, $v_position+1);
          }
          // ----- Change potential windows directory separator
          if ((strpos($p_path, '\\') > 0) || (substr($p_path, 0,1) == '\\')) {
              $p_path = strtr($p_path, '\\', '/');
          }
      }
      return $p_path;
    }
    // }}}

}
//@END@
?>
