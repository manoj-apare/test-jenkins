<?php
include ('connection.php');
?>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>File Download Data</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $( function() {
            $( "#datepicker1" ).datepicker();
            $( "#datepicker2" ).datepicker();
            $("input[name='submit']").click(function(){
                $("#main-header").text("File download details");
            });
            $("#btnExport").click(function (e) {
                window.open('data:application/vnd.ms-excel,' + encodeURIComponent($('#dvData').html()));
                e.preventDefault();
            });
        });
    </script>
    <style>
        table thead th{
            background-color:#005697;
            color:#fff;
        }
        table tfoot{
            text-align:right;
            background-color:#005697;
            color:#fff;
        }
        tbody tr td{
            text-align:center;
        }
        input[type='submit']{
            background-color:#005697;
            color:#fff;
        }
        input[type='button']{
            background-color:#005697;
            color:#fff;
        }
        button{
            background-color:#005697;
            color:#fff;
        }
        #dvData table{
            width:100%;
        }
    </style>
</head>
<body>
<table>
    <form action="" method="post">
        <tr><td>From: <input type="text" name="datepicker1" id="datepicker1" value="<?php echo isset($_POST['datepicker1']) ? $_POST['datepicker1'] : '' ?>" required></td><td><td>To: <input type="text" name="datepicker2" id="datepicker2" value="<?php echo isset($_POST['datepicker2']) ? $_POST['datepicker2'] : '' ?>" required></td>
            <td><input type="submit" name="submit" value="Search"/></td><td><input type="button" id="btnExport" value="Export" /></td></tr>
    </form>
</table>
<div id="dvData">
    <table>
        <thead>
        <th>Spec Number</th>
        <th>Spec Revision</th>
        <th>File Name</th>
        <th>Count</th>
        </thead>
        <?php
        if(isset($_POST["submit"])){
            $show_header = 1;
            $from = $_POST["datepicker1"];
            $to = $_POST["datepicker2"];
            $from = strtotime($from);
            $to = strtotime($to);
            $total_count = 0;
            $sql = "select ps.field_spec_number_value, fm.fid, count(fd.entity_id) AS down_count, sr.field_spec_revision_value, fm.filename, ff.field_file_description, fd.created from cypressext_prod.file_download_entity_field_data fd, cypressext_prod.file_managed fm, cypressext_prod.paragraph__field_spec_revision sr, cypressext_prod.paragraph__field_spec_number ps, cypressext_prod.paragraph__field_file ff where fd.entity_id = fm.fid AND fm.fid = ps.entity_id AND sr.entity_id = ps.entity_id AND ps.entity_id = ff.field_file_target_id AND  entity_type = 'file' AND (fd.created BETWEEN $from AND $to) GROUP BY fm.fid";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
					<td>" . $row["field_spec_number_value"] . "</td>
					<td>" . $row["field_spec_revision_value"] . "</td>
					<td>" . $row["filename"] . "</td>
					<td>" . $row["down_count"] . "</td>
					</tr>";
                    $total_count = $total_count + $row["down_count"];
                }
                echo "<tfoot>
						<tr>
							<td></td>
							<td></td>
							<td>Total</td>
							<td>".$total_count."</td>
						</tr></tfoot>
					";
            }else {

            }
        }
        ?>

    </table>
</div>
</body>
</html>
