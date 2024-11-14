<?php
class UserDataAccess{
	
	private $link;

	/**
	 * Constructor
	 *
	 * @param connection $link 	The link the the database 		
	 */
	function UserDataAccess($link){
		$this->link = $link;
	}

	/**
	* Authenticates a user for accessing the web site (or the control panel of the site)
	* 
	* @param string email
	* @param string password
	* 
	* @return array If login is authenticated returns the user object (as assoc array or User obj???).
	* 				Returns false if authentication fails (or if something goes wrong).
	*/
	function login($email, $password){
	
		// Prevent SQL injection by scrubbing the email and password
		// with mysqli_real_escape_string();
		$email = mysqli_real_escape_string($this->link, $email);
		$password = mysqli_real_escape_string($this->link, $password);

		$qStr = "SELECT
					user_id, user_first_name, user_last_name, user_email, user_role, user_salt, user_password
				FROM users 
				WHERE user_email = '" . $email . "' AND user_active='yes'";
		
		// this comes in really handy when you are testing your query....
		//die($qStr);

		
		$result = mysqli_query($this->link, $qStr) or $this->handle_error(mysqli_error($this->link));
		
		if($result){

			$row = mysqli_fetch_assoc($result);
			
			$salt = $row['user_salt'];
			if($this->encrypt_password($salt, $password) == $row['user_password']){

				// scrub the data to prevent XSS attacks
				$user = array();
				$user['user_id'] = htmlentities($row['user_id']);
				$user['user_first_name'] = htmlentities($row['user_first_name']);
				$user['user_last_name'] = htmlentities($row['user_last_name']);
				$user['user_email'] = htmlentities($row['user_email']);
				$user['user_role'] = htmlentities($row['user_role']);

				return $user;
			}
		}

		return false;
	}

	/**
	* Gets all users
	* 
	* @return array Returns an array of User objects??? 
	* 				Or an array of associative arrays???
	*/
	function get_all_users(){
		$qStr = "SELECT
					user_id, user_first_name, user_last_name, user_email, user_role, user_active
				FROM users";
		
		//die($qStr);

		$result = mysqli_query($this->link, $qStr) or $this->handle_error(mysqli_error($this->link));
		
		$all_users = array();

		while($row = mysqli_fetch_assoc($result)){

			// create a $user obj and scrub the data to prevent XSS attacks
			$user = array();
			$user['user_id'] = htmlentities($row['user_id']);
			$user['user_first_name'] = htmlentities($row['user_first_name']);
			$user['user_last_name'] = htmlentities($row['user_last_name']);
			$user['user_email'] = htmlentities($row['user_email']);
			$user['user_role'] = htmlentities($row['user_role']);

			// add the $user to the $all_users array
			$all_users[] = $user;
		}

		return $all_users;
			
	}

	/**
	* Gets a user by the id that is passed in.
	*
	* @param number 	The id of he user to get
	*
	* @return array 	Returns an assoc array (or a User obj?)
	* 					Returns false if something goes wrong.
	*/
	function get_user_by_id($id){

		$qStr = "SELECT
					user_id, user_first_name, user_last_name, user_email, user_role, user_password, user_active, user_image
				FROM users
				WHERE user_id = " . mysqli_real_escape_string($this->link, $id);
		
		//die($qStr);

		$result = mysqli_query($this->link, $qStr) or $this->handle_error(mysqli_error($this->link));
		
		if($result->num_rows == 1){

			$row = mysqli_fetch_assoc($result);

			$user = array();
			$user['user_id'] = htmlentities($row['user_id']);
			$user['user_first_name'] = htmlentities($row['user_first_name']);
			$user['user_last_name'] = htmlentities($row['user_last_name']);
			$user['user_email'] = htmlentities($row['user_email']);
			$user['user_role'] = htmlentities($row['user_role']);
			$user['user_password'] = htmlentities($row['user_password']);
			$user['user_active'] = htmlentities($row['user_active']);
			$user['user_image'] = htmlentities($row['user_image']);

			return $user;
			
		}else{
			$this->handle_error("something went wrong");
		}
	}

	/**
	* Inserts a new user into the Users table
	*
	* @param array 		An obj/array that has the following properties: 
	*					user_first_name, user_last_name, user_email, user_role, user_password, user_active
	*
	* @return array 	Returns an assoc array, along with the new users id
	* 					Returns false if something goes wrong.
	*/
	function insert_user($user){

		// prevent SQL injection
		$user['user_first_name'] = mysqli_real_escape_string($this->link, $user['user_first_name']);
		$user['user_last_name'] = mysqli_real_escape_string($this->link, $user['user_last_name']);
		$user['user_email'] = mysqli_real_escape_string($this->link, $user['user_email']);
		$user['user_role'] = mysqli_real_escape_string($this->link, $user['user_role']);
		//$user['user_password'] = mysqli_real_escape_string($this->link, $user['user_password']);
		$user['user_active'] = mysqli_real_escape_string($this->link, $user['user_active']);
		$user['user_image'] = mysqli_real_escape_string($this->link, $user['user_image']);

		//secure the password
		$salt = $this->get_password_salt();
		$password = $this->encrypt_password($salt, $user['user_password']);

		$qStr = "INSERT INTO users (
					user_first_name,
					user_last_name, 
					user_email, 
					user_password, 
					user_salt, 
					user_role, 
					user_active,
					user_image
				) VALUES (
					'{$user['user_first_name']}',
					'{$user['user_last_name']}',
					'{$user['user_email']}', 
					'{$password}', 
					'{$salt}', 
					'{$user['user_role']}', 
					'{$user['user_active']}',
					'{$user['user_image']}'
				)";
		
		//die($qStr);

		$result = mysqli_query($this->link, $qStr) or $this->handle_error(mysqli_error($this->link));

		if($result){
			// add the user id that was assigned by the data base
			$user['user_id'] = mysqli_insert_id($this->link);
			// then return the user
			return $user;
		}else{
			$this->handle_error("unable to insert user");
		}

		return false;
	}

	/**
	* Updates an existing user in the Users table
	*
	* @param array 		An obj/array that has the following properties: 
	*					user_id,user_first_name, user_last_name, user_email, user_role, user_password, user_active
	*
	* @return array 	Returns an assoc array with all the user properties
	* 					Returns false if something goes wrong.
	*/
	function update_user($user){

		// prevent SQL injection
		$user['user_id'] = mysqli_real_escape_string($this->link, $user['user_id']);
		$user['user_first_name'] = mysqli_real_escape_string($this->link, $user['user_first_name']);
		$user['user_last_name'] = mysqli_real_escape_string($this->link, $user['user_last_name']);
		$user['user_email'] = mysqli_real_escape_string($this->link, $user['user_email']);
		$user['user_role'] = mysqli_real_escape_string($this->link, $user['user_role']);
		//$user['user_password'] = mysqli_real_escape_string($this->link, $user['user_password']);
		$user['user_active'] = mysqli_real_escape_string($this->link, $user['user_active']);
		$user['user_image'] = mysqli_real_escape_string($this->link, $user['user_image']);

		//secure the password
		$salt = $this->get_password_salt();
		$password = $this->encrypt_password($salt, $user['user_password']);

		$qStr = "UPDATE users SET
					user_first_name = '{$user['user_first_name']}',
					user_last_name = '{$user['user_last_name']}',
					user_email = '{$user['user_email']}', 
					user_password = '{$password}',
					user_salt = '{$salt}', 
					user_role = '{$user['user_role']}',  
					user_active = '{$user['user_active']}',
					user_image = '{$user['user_image']}'
				WHERE user_id = " . $user['user_id'];
					
		//die($qStr);

		$result = mysqli_query($this->link, $qStr) or $this->handle_error(mysqli_error($this->link));

		if($result){
			return $user;
		}else{
			$this->handle_error("unable to update user");
		}

		return false;
	}

	/**
	* Generates salt (a random string) for securing passords
	*
	* @return string 	returns the salt string
	*/
	function get_password_salt(){
		//$bytes = random_bytes(5);
		//return bin2hex($bytes);

		return mcrypt_create_iv(5);
	}

	/**
	* Encrypts a password, using the salt provided.
	*
	* @param $salt 		The string used to salt the encrption
	* @param $password 	The password (string) to encrypt
	*/
	function encrypt_password($salt, $password){
		return md5($salt . $password . $salt);
	}

	/**
	* Gets all the user roles from the db
	*
	* @return array
	*/
	function get_user_roles(){
		
		$qStr = "SELECT user_role_id, user_role_name FROM user_roles";
		$result = mysqli_query($this->link, $qStr) or $this->handle_error(mysqli_error($this->link));

		$all_roles = array();

		while($row = mysqli_fetch_assoc($result)){
			$role = array();
			$role['user_role_id'] = htmlentities($row['user_role_id']);
			$role['user_role_name'] = htmlentities($row['user_role_name']);
			$all_roles[] = $role;
		}

		return $all_roles;
	}

	/**
	* Handles errors in UserDataAccess
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