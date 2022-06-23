<?
class fileio{
	var $working_dir;
	function getList(){
		$retVal = false;
		$d = dir($this->working_dir);

		while (false !== ($entry = $d->read())) {
			if('.' !== $entry && '..' !== $entry && is_file("$this->working_dir/$entry")){
				$size = filesize("$this->working_dir/$entry");
				if($size){
					$retVal[] =array(
					'name'	=>$entry,
					'size'	=> $size,
					'date'	=> fileatime("$this->working_dir/$entry")
					);
				}
			}
		}
		$d->close();
		return $retVal;
	}

	function process_upload(){
		global $_FILES;
		$retVal = false;
		$disallowed_ext = array('.php', '.php3', '.php4', '.shtml', '.pl', '.jsp', '.cgi','.exe');
		$file_field = $_FILES['file'];

		//detect if there are any uploaded files in $_FILES; return false if not
		if($file_field['size'] != 0){
			$path_parts = pathinfo($file_field['name']);
			$ext = '.' . strtolower($path_parts["extension"]);
			if(in_array($ext, $disallowed_ext)){
				die("Wrong file type ($ext). Please upload other file types than : " . implode(' ',$disallowed_ext) );
			}

			$new_name = $file_field['name'];
			if(move_uploaded_file($file_field['tmp_name'], "$this->working_dir/$new_name")){
				$retVal[] = $new_name;
			}

		}
		return $retVal;
	}

	function delete($file){
		$path_parts = pathinfo($file);
		$file = $path_parts['basename'];
		@unlink("$this->working_dir/$file");
		return;
	}

	function download($file){
		$path_parts = pathinfo($file);
		$file = $path_parts['basename'];
		$fileName = "$this->working_dir/$file";
		if(!$fdl=@fopen($fileName,'r')){
			die("Cannot Open File!");
		} else {
			header("Cache-Control: ");// leave blank to avoid IE errors
			header("Pragma: ");// leave blank to avoid IE errors
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=\"".$file."\"");
			header("Content-length:".(string)(filesize($fileName)));
			sleep(1);
			fpassthru($fdl);
		}
		return;
	}
}
session_start();

if(!$op) $op='start';
if(!is_dir('fileserver')) mkdir('fileserver',0777);
if($id)
{
	if(!is_dir('fileserver/'.$id)) mkdir('fileserver/'.$id,0777);
}
$fileio = new fileio();
$fileio->working_dir = 'fileserver/'.$id;
if(!is_dir($fileio->working_dir))die("Folder $fileio->working_dir no existe. Paila");


switch ($op){
   case 'add':
		{
			if(isset($_FILES['file']))
			{
				$ret = $fileio->process_upload();
				header("Location: files.php?op=start&id=$id");
				return;
			}
		}
		break;
   case 'delete':
	   {
			$fileio->delete($_REQUEST['Id']);
			header("Location: files.php?op=start&id=$id");
			return;
		}
		break;
   case 'download':
	   {
			$fileio->download($_REQUEST['Id']);
			return;
		}
		break;
   case 'start':
		{
			$fileList = $fileio->getList();
		}
		break;
}
include('inc/files.tpl');
?>
