<?php

$insert=false;
$update=false;
$delete=false;

$servername="localhost";
$username="root";
$password="";
$database="notes";

//Create a connection
$conn=mysqli_connect($servername,$username,$password,$database);

//Die if connection was not successful
if(!$conn){
    die("Sorry we failed to connect:".mysqli_connect_error());
}
//Delete the record
if(isset($_GET['delete'])){
  $sno= $_GET['delete'];
  $delete=true;
  //sql query to be executed
  $sql="DELETE FROM `notes` WHERE `sno` = $sno";
  $result=mysqli_query($conn,$sql);
}
//Update the record
if($_SERVER['REQUEST_METHOD']=='POST'){
  if(isset($_POST['snoEdit'])){
    $sno=$_POST["snoEdit"];
    $title=$_POST["titleEdit"];
    $description=$_POST["descriptionEdit"];
    //sql query to be executed
    $sql="UPDATE `notes` SET `title`='$title' , `description` = '$description' WHERE `notes`.`sno` = $sno";
    $result=mysqli_query($conn,$sql);
    if($result){
      $update=true;
    }
  }
  else{
    $title=$_POST["title"];
    $description=$_POST["description"];
    //sql query to be executed
    $sql="INSERT INTO `notes` (`title`, `description`) VALUES ('$title', '$description')";
    $result=mysqli_query($conn,$sql);
    if($result){
        $insert=true;
    }
    else{
        echo "The record was not inserted successfully because-->".mysqli_error($conn);
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU"
      crossorigin="anonymous"
    />
    <link rel="stylesheet" href="//cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <title>i Notes - Note making</title>
  </head>
  <body>
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Edit this Note</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="/crudapp/index.php" method="post">
      <div class="modal-body">
        <input type="hidden" name="snoEdit" id="snoEdit">
          <div class="mb-3">
            <label for="title" class="form-label">Note Title</label>
            <input
              type="text"
              class="form-control"
              id="titleEdit"
              name="titleEdit"
              aria-describedby="emailHelp"
            />
          </div>
          <div class="mb-3">
            <label for="desc" class="form-label">Note Description</label>
            <textarea
              class="form-control"
              id="descriptionEdit"
              name="descriptionEdit"
              rows="3"
            ></textarea>
          </div>
      </div>
      <div class="modal-footer d-block mr-auto">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
      </div>
      </form>
    </div>
  </div>
</div>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container-fluid">
        <a class="navbar-brand" href="#"><img src="/crudapp/logo.svg" height="30px" alt=""></a>
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="#">Home</a>
            </li>
            <!-- <li class="nav-item">
              <a class="nav-link" href="#">About</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">Contact Us</a>
            </li> -->
          </ul>
        </div>
      </div>
    </nav>

    <?php
      if($insert){
        echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
        <strong>Success!</strong> Your note has been inserted successfully
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
      </div>";
      }
    ?>
    <?php
      if($delete){
        echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
        <strong>Success!</strong> Your note has been deleted successfully
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
      </div>";
      }
    ?>
    <?php
      if($update){
        echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
        <strong>Success!</strong> Your note has been updated successfully
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
      </div>";
      }
    ?>

    <div class="container my-4">
      <h2>Add a Note</h2>
      <form action="/crudapp/index.php" method="post">
        <div class="mb-3">
          <label for="title" class="form-label">Note Title</label>
          <input
            type="text"
            class="form-control"
            id="title"
            name="title"
            aria-describedby="emailHelp"
          />
        </div>
        <div class="mb-3">
          <label for="desc" class="form-label">Note Description</label>
          <textarea
            class="form-control"
            id="description"
            name="description"
            rows="3"
          ></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Add Note</button>
      </form>
    </div>

    <div class="container my-4">
      <table class="table" id="myTable">
        <thead>
          <tr>
            <th scope="col">S.No</th>
            <th scope="col">Title</th>
            <th scope="col">Description</th>
            <th scope="col">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php
          $sql="SELECT * FROM `notes`";
          $result=mysqli_query($conn,$sql);
          $sno=0;
          while($row=mysqli_fetch_assoc($result)){
            $sno=$sno+1;
            echo "<tr>
            <th scope='row'>".$sno."</th>
            <td>".$row['title']."</td>
            <td>".$row['description']."</td>
            <td><button class='edit btn btn-sm btn-primary' id=".$row['sno'].">Edit</button> <button class='delete btn btn-sm btn-primary' id=d".$row['sno'].">Delete</button>
            </tr>";
          }
        ?>
        </tbody>
      </table>
    </div>
    <hr>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="script.js"></script>
  </body>
</html>
