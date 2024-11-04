<div class="card">
    <div class="card-body">
        <form action="<?=base_url()?>devs/devs2/reading_files" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="file_input">File Excel</label>
                <input type="file" name="file_input" class="form-control">
            </div>
            <div class="form-group">
                <label for="file_input">X-Target</label>
                <input type="text" name="target" class="form-control" value="read_ecoll">
            </div>
            <button class="btn btn-primary" type="submit" name="submit_form">Submit</button>
        </form>
    </div>
</div>