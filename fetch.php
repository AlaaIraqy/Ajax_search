<?php
$sernamename = "localhost";
	$username    = "root";
	$passoword   = "";
	$databasename= "lab4";

	// create database connection
	$con = mysqli_connect($sernamename, $username,$passoword,$databasename);

	// check connection
	if ($con->connect_error) {
		die("Connection failed". $con->connect_error);
    }
    
    $limit=5;
    $page=1;
    if($_POST['page']>1){
        $start=(($_POST['page']-1)*$limit);
        $page=$_POST['page'];
    }else{
        $start=0;
    }

    $query="SELECT course_name,department_name,professor_name,course_description
    FROM departments d,courses c,professors p
    WHERE p.professor_id=c.prof_id AND d.department_id=c.dept_id";
    $filter_query = $query . ' LIMIT '.$start.', '.$limit.' ';
    if($_POST['query'] != ''){
    $query = ' SELECT * 
            from courses,professors,departments
            WHERE course_name LIKE  
            "%' . str_replace(' ', '%', $_POST['query']) . '%" OR
            course_description LIKE  
            "%' . str_replace(' ', '%', $_POST['query']) . '%" OR
            professor_name LIKE  
            "%' . str_replace(' ', '%', $_POST['query']) . '%" OR
            department_name LIKE  
            "%' . str_replace(' ', '%', $_POST['query']) . '%"
            ';
    }
    $query .= ' ORDER BY course_id ASC';
    $filter_query = $query .' LIMIT '.$start.', '.$limit.'';

    if($statement = mysqli_query($con,$query)){
        $total_data = mysqli_num_rows($statement);
    }
   
   if($statement = mysqli_query($con,$filter_query)){
    $result=mysqli_fetch_all($statement, MYSQLI_ASSOC);
    $total_filter_data = mysqli_num_rows($statement);
   }
   
$output = '
<label>Total Records - '.$total_data.'</label>
<table class="table table-striped table-bordered">
  <tr>
    <th>Course name</th>
    <th>Department name</th>
    <th>Professor name</th>
    <th>Course description</th>
  </tr>
';
if($total_data > 0)
{
  foreach($result as $row)
  {
    $output .= '
    <tr>
      <td>'.$row["course_name"].'</td>
      <td>'.$row["department_name"].'</td>
      <td>'.$row["professor_name"].'</td>
      <td>'.$row["course_description"].'</td>
    </tr>
    ';
  }
}
else
{
  $output .= '
  <tr>
    <td colspan="2" align="center">No Data Found</td>
  </tr>
  ';
}
$output .= '
</table>
<br />
<div align="center">
  <ul class="pagination">
';

$total_links = ceil($total_data/$limit);
$previous_link = '';
$next_link = '';
$page_link = '';


if($total_links > 4)
{
  if($page < 5)
  {
    for($count = 1; $count <= 5; $count++)
    {
      $page_array[] = $count;
    }
    $page_array[] = '...';
    $page_array[] = $total_links;
  }
  else
  {
    $end_limit = $total_links - 5;
    if($page > $end_limit)
    {
      $page_array[] = 1;
      $page_array[] = '...';
      for($count = $end_limit; $count <= $total_links; $count++)
      {
        $page_array[] = $count;
      }
    }
    else
    {
      $page_array[] = 1;
      $page_array[] = '...';
      for($count = $page - 1; $count <= $page + 1; $count++)
      {
        $page_array[] = $count;
      }
      $page_array[] = '...';
      $page_array[] = $total_links;
    }
  }
}
else
{
  for($count = 1; $count <= $total_links; $count++)
  {
    $page_array[] = $count;
  }
}
if(!$total_data == 0) {
for($count = 0; $count < count($page_array); $count++)
{
  if($page == $page_array[$count])
  {
    $page_link .= '
    <li class="page-item active">
      <a class="page-link" href="#">'.$page_array[$count].' <span class="sr-only">(current)</span></a>
    </li>
    ';

    $previous_id = $page_array[$count] - 1;
    if($previous_id > 0)
    {
      $previous_link = '<li class="page-item"><a class="page-link" href="javascript:void(0)" data-page_number="'.$previous_id.'">Previous</a></li>';
    }
    else
    {
      $previous_link = '
      <li class="page-item disabled">
        <a class="page-link" href="#">Previous</a>
      </li>
      ';
    }
    $next_id = $page_array[$count] + 1;
    if($next_id > $total_links)
    {
      $next_link = '
      <li class="page-item disabled">
        <a class="page-link" href="#">Next</a>
      </li>
        ';
    }
    else
    {
      $next_link = '<li class="page-item"><a class="page-link" href="javascript:void(0)" data-page_number="'.$next_id.'">Next</a></li>';
    }
  }
  else
  {
    if($page_array[$count] == '...')
    {
      $page_link .= '
      <li class="page-item disabled">
          <a class="page-link" href="#">...</a>
      </li>
      ';
    }
    else
    {
      $page_link .= '
      <li class="page-item"><a class="page-link" href="javascript:void(0)" data-page_number="'.$page_array[$count].'">'.$page_array[$count].'</a></li>
      ';
    }
  }
}
}
$output .= $previous_link . $page_link . $next_link;
$output .= '
  </ul>

</div>
';

echo $output;

?>