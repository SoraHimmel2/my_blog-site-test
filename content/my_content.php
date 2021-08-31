<div class="contaier">
<div class="row m-4">



  <?php
  $conn = mysqli_connect('localhost','SoraHimmel','1234567890','test_database');

  if(!$conn){
  	 echo 'Connection eror: ' . mysqli_connect_error();
   }
  $sql = 'SELECT title,id, description FROM novels ORDER BY id';
  $result = mysqli_query($conn,$sql);
  $novels = mysqli_fetch_all($result, MYSQLI_ASSOC);
  mysqli_free_result($result);
  mysqli_close($conn);

  ?>



<?php if(isset($novels) && is_array($novels))
foreach($novels as $novel) {  ?>



      <div class="col-md-6  d-md-flex ">
      <div class="card m-2 rounded-0 flex-fill">
        <div class="overflow-auto"style="max-height:230px;">
           <img src="my_images/<?php echo $novel['id']; ?>.jpg" class="card-img-top rounded-0 " alt="...">
        </div>


      <div class="card-body d-flex flex-column">
        <h5 class="card-title"> <?php echo htmlspecialchars($novel['title']); ?> </h5>
        <p class="card-text"> <?php echo htmlspecialchars($novel['description']); ?></p>
       <div class="mt-auto">

      <a href="#" class="btn btn-primary">Go somewhere</a>
       </div>
      </div>
      </div>
         </div>

<?php } ?>

</div>
</div>
