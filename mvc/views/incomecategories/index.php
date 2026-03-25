<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa fa-clipboard"></i> Income Categories</h3>
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li class="active"> <a href="<?=base_url("income/index")?>"> <?=$this->lang->line('menu_income')?> </a> </li>
            <li class="active"> Income Categories </li>
        </ol>
    </div><!-- /.box-header -->
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <h5 class="page-header">
                    <a href="javascript:void(0)" onclick="addCategory()">
                        <i class="fa fa-plus"></i> 
                        Add Income Category
                    </a>
                </h5>

                <div id="hide-table">
                    <table id="example1" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="col-sm-2">#</th>
                                <th class="col-sm-3">Name</th>
                                <th class="col-sm-4">Note</th>
                                <th class="col-sm-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(customCompute($incomecategories)) {$i = 1; foreach($incomecategories as $category) { ?>
                                <tr>
                                    <td data-title="#">
                                        <?php echo $i; ?>
                                    </td>
                                    <td data-title="Name">
                                        <?php echo $category->name; ?>
                                    </td>
                                    <td data-title="Note">
                                        <?php echo $category->note; ?>
                                    </td>
                                    <td data-title="Action">
                                        <a href="javascript:void(0)" onclick="editCategory(<?=$category->incomecategoriesID?>)" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i></a>
                                        <a href="<?=base_url("incomecategories/delete/".$category->incomecategoriesID)?>" onclick="return confirm('you are about to delete a record. This cannot be undone. are you sure?')" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php $i++; }} ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Add/Edit -->
<div class="modal fade" id="categoryModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modalTitle">Add Income Category</h4>
      </div>
      <form id="categoryForm" class="form-horizontal">
        <div class="modal-body">
            <input type="hidden" name="incomecategoriesID" id="incomecategoriesID">
            <div class="form-group" id="nameDiv">
                <label for="name" class="col-sm-3 control-label">Name <span class="text-red">*</span></label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" id="m_name" name="name">
                    <span class="text-red" id="name_error"></span>
                </div>
            </div>
            <div class="form-group" id="noteDiv">
                <label for="note" class="col-sm-3 control-label">Note</label>
                <div class="col-sm-8">
                    <textarea class="form-control" id="m_note" name="note" rows="3"></textarea>
                    <span class="text-red" id="note_error"></span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-success" id="saveBtn">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
    function addCategory() {
        $('#categoryForm')[0].reset();
        $('#incomecategoriesID').val('');
        $('#modalTitle').text('Add Income Category');
        $('.form-group').removeClass('has-error');
        $('.text-red').text('');
        $('#categoryModal').modal('show');
    }

    function editCategory(id) {
        $('#categoryForm')[0].reset();
        $('.form-group').removeClass('has-error');
        $('.text-red').text('');
        $.ajax({
            type: 'GET',
            url: "<?=base_url('incomecategories/edit')?>",
            data: {id: id},
            dataType: "json",
            success: function(data) {
                $('#incomecategoriesID').val(data.incomecategoriesID);
                $('#m_name').val(data.name);
                $('#m_note').val(data.note);
                $('#modalTitle').text('Edit Income Category');
                $('#categoryModal').modal('show');
            }
        });
    }

    $('#saveBtn').click(function() {
        var id = $('#incomecategoriesID').val();
        var url = id ? "<?=base_url('incomecategories/edit')?>" : "<?=base_url('incomecategories/add')?>";
        
        $.ajax({
            type: 'POST',
            url: url,
            data: $('#categoryForm').serialize(),
            dataType: "json",
            success: function(data) {
                if(data.status) {
                    location.reload();
                } else {
                    if(data.name) {
                        $('#nameDiv').addClass('has-error');
                        $('#name_error').text(data.name);
                    } else {
                        $('#nameDiv').removeClass('has-error');
                        $('#name_error').text('');
                    }
                    if(data.note) {
                        $('#noteDiv').addClass('has-error');
                        $('#note_error').text(data.note);
                    } else {
                        $('#noteDiv').removeClass('has-error');
                        $('#note_error').text('');
                    }
                }
            }
        });
    });
</script>
