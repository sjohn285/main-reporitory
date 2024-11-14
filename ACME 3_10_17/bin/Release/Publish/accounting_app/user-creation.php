<?php
$page_title = "ACME's Home Page";
include("acmeHeader.inc.php");
?>
    <form id="userCreationForm" action="" method="POST">      
        <div id="content">
            <div class="form-row">
                <div class="form-label">
                    <label>First Name: </label>
                </div>
                <div class="form-control">
                    <input type="text" class="control" name="txtFirstName" />
                </div>
            </div>
            <div class="form-row">
                <div class="form-label">
                    <label>Last Name: </label>
                </div>
                <div class="form-control">
                    <input type="text" class="control" name="txtLastName" />
                </div>
            </div>
            <div class="form-row">
                <div class="form-label">
                    <label>Password: </label>
                </div>
                <div class="form-control">
                    <input type="text" class="control" name="txtPassword" />
                </div>
            </div>
            <div class="form-row">
                <div class="form-label">
                    <label>Re-enter Password: </label>
                </div>
                <div class="form-control">
                    <input type="text" class="control" name="txtReEnterPassword" />
                </div>
            </div>
            <div class="form-row">
                <div class="form-control">
                    <input type="button" id="Signup" value="Sign up" />
                </div>
            </div>
        </div> 
    </form>
<?php
include("acmeFooter.inc.php");
?>