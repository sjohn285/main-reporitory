<?php
class FileDataAccess{
	
	private $link;

	/**
	 * Constructor
	 *
	 * @param connection $link 	The link the the database 		
	 */
	function __construct($link){
		$this->link = $link;
	}

	
	/**
	* Inserts a new file into the files table
	*
	* @param array 		An obj/array that has the following properties: 
	*					user_first_name, user_last_name, user_email, user_role, user_password, user_active
	*
	* @return array 	Returns an assoc array, along with the new users id
	* 					Returns false if something goes wrong.
	*/
	function insert_file($file){

		// prevent SQL injection
		$file['file_name'] = mysqli_real_escape_string($this->link, $file['file_name']);
		$file['file_extension'] = mysqli_real_escape_string($this->link, $file['file_extension']);
		$file['file_size'] = mysqli_real_escape_string($this->link, $file['file_size']);
		$file['file_uploaded_by_id'] = mysqli_real_escape_string($this->link, $file['file_uploaded_by_id']);
		$file['file_uploaded_date'] = mysqli_real_escape_string($this->link, $file['file_uploaded_date']);

		// set up the query to insert a file
		$qStr = "INSERT INTO files (
					file_name, 
					file_extension, 
					file_size, 
					file_uploaded_by_id, 
					file_uploaded_date
				) VALUES (
					'{$file['file_name']}', 
					'{$file['file_extension']}', 
					'{$file['file_size']}', 
					'{$file['file_uploaded_by_id']}', 
					'{$file['file_uploaded_date']}'
				)";
		
		//die($qStr);

		$result = mysqli_query($this->link, $qStr) or $this->handle_error(mysqli_error($this->link));

		if($result){
			// add the file id that was assigned by the data base
			$file['file_id'] = mysqli_insert_id($this->link);
			// then return the $file array (with the file-id)
			return $file;
		}else{
			$this->handle_error("unable to insert file");
		}

		return false;
	}

	
	/**
	* Uploads a file to the server.
	*
	* @throws					Throws an Exception if something goes wrong.
	* 
	* @param $file_input_name 	The name of the input tag, it will be used to
	* 							access the uploaded file like this $_FILES[$file_input_name]
	* @param $allowed_types 	An array the has the allowed mime types that can be uploaded
	* @param $upload_dir		The folder to upload to (Note: it must have the trailing /)
	*
	* @return 					Retruns true if everything succeeds (Throws an error if it doesn't)
	*/
	function upload_file($file_input_name, $allowed_types, $upload_dir){

		// make sure the file input actually exists
		if(isset($_FILES[$file_input_name])){
		    
		    // make sure the file being uploaded is allowed		    
		    if(in_array($_FILES[$file_input_name]['type'], $allowed_types)){
		      		      	
		      	$file_name = $_FILES[$file_input_name]['name'];
		    	
		    	// the the final destination for the uploaded file (this include the path and file name)
		      	$file_path = $upload_dir . $file_name;

		      	// move the file from the tmp dir to it's final destination
		      	if(move_uploaded_file($_FILES[$file_input_name]['tmp_name'], $file_path)){
		        	return true;
		      	}else{
		        	throw new Exception("Unable to upload file");
		      	}

		    }else{
		    	throw new Exception("Invalid file type");
		    }

	  }else{
	    throw new Exception("No file posted");
	  }

	}

	
	/**
	* Gets the extension for a file name. For example, if you pass
	* in somefile.txt, it will return 'txt'
	*
	* @param $file_name 	The name of the file to get the extension for
	* @return 				The file's extension.
	*/
	function get_file_extension($file_name){
		$parts = explode(".", $file_name);
		
		//Not sure if we should really throw an exception here!
		if(count($parts) < 2){
			$this->handle_error("$file_name has no file extension");
		}

		return array_pop($parts);
	}

	

	/**
	* Handles errors in FileDataAccess
	* 
	* @param array Returns an array of User objects??? Or an array of associative arrays???
	*/
	function handle_error($msg){
		// how do we want to handle this? should we throw an exception
		// and let our custom EXCEPTION handler deal with it?????
		$stack_trace = print_r(debug_backtrace(), true);
		throw new Exception($msg . " - " . $stack_trace);
	}

	// NOTE: we could make a DataAccess super class that has handle_error()
	// in it. Then we could sub class it and all sub classes could share the
	// same method (less code duplication)
}