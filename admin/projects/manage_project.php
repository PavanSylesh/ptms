<?php
require_once('../../config.php');
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT * FROM `project_list` where id = '{$_GET['id']}'");
    if($qry->num_rows > 0){
        $res = $qry->fetch_array();
        foreach($res as $k => $v){
            if(!is_numeric($k))
            $$k = $v;
        }
    }
    $query = $conn->query("SELECT id workID,name FROM work_type_list");
    if($query->num_rows > 0){
        while ($row = $query->fetch_assoc()) {
            $work_list[] = $row;
        }
    }
    $qry = $conn->query("SELECT DISTINCT wtl.id workID,wtl.name FROM work_type_list wtl 
                            JOIN proj_worklist_rel pwr ON wtl.id = pwr.worklist_id 
                            JOIN project_list pl ON pwr.proj_id = pl.id WHERE pl.id = '$id'");

    if($qry->num_rows > 0){
        while ($row = $qry->fetch_assoc()) {
            $tagged[] = $row;
        }
    }
        foreach($tagged as $taggedName){
            $wlname[] = $taggedName['name'];
        }
        var_dump($wlname);
} else{
    $query = $conn->query("SELECT id workID,name FROM work_type_list");
    if($query->num_rows > 0){
        while ($row = $query->fetch_assoc()) {
            $work_list[] = $row;
        }
    }
}
?>
<style>
	img#cimg{
		height: 17vh;
		width: 25vw;
		object-fit: scale-down;
	}
</style>
<div class="container-fluid">
    <form action="" id="project-form">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <div class="form-group">
            <label for="name" class="control-label">Project Name</label>
            <input type="text" name="name" id="name" class="form-control form-control-border" placeholder="Enter Project Name" value ="<?php echo isset($name) ? $name : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="description" class="control-label">Description</label>
            <textarea rows="3" name="description" id="description" class="form-control form-control-sm rounded-0" required><?php echo isset($description) ? ($description) : '' ?></textarea>
        </div>
        <div class="form-group col-md-4">
            <label for="name" class="control-label">Work Type Name</label>
                <select name="work_id[]" id="work_id" class="form-control form-control-sm form-control-border" multiple>
                    <?php
                        foreach ($work_list as $list) {
                            $workId = $list['id'];
                            $workName = $list['name'];
                            if(isset($_GET['id'])){
                            if(in_array($workName,$wlname)){
                                echo "<option value='$workId' selected>$workName</option>";
                            }else{
                                echo "<option value='$workId'>$workName</option>";
                            }
                            }
                            else{
                                echo "<option value='$workId'>$workName</option>";
                            }
                        }
                    ?>
                </select>
            </div>
    </form>
</div>
<script>
    $(function(){
        $('#uni_modal #project-form').submit(function(e){
            e.preventDefault();
            var _this = $(this)
            $('.pop-msg').remove()
            var el = $('<div>')
                el.addClass("pop-msg alert")
                el.hide()
            start_loader();
            $.ajax({
                url:_base_url_+"classes/Master.php?f=save_project",
				data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
				error:err=>{
					console.log(err)
					alert_toast("An error occured",'error');
					end_loader();
				},
                success:function(resp){
                    if(resp.status == 'success'){
                        location.reload();
                    }else if(!!resp.msg){
                        el.addClass("alert-danger")
                        el.text(resp.msg)
                        _this.prepend(el)
                    }else{
                        el.addClass("alert-danger")
                        el.text("An error occurred due to unknown reason.")
                        _this.prepend(el)
                    }
                    el.show('slow')
                    $('html,body,.modal').animate({scrollTop:0},'fast')
                    end_loader();
                }
            })
        })
    })
</script>