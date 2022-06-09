<div class="modal fade" id="editBookmarkModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-fullscreen-lg-down">
        <div class="modal-content">
            <form id="editBookmarkForm" action="" method="post">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="editBookmarkHeader"></h5>
                    <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="d-flex flex-wrap mb-3" style="column-gap: 1rem;">
                        <div class="flex-grow-1">
                            <label for="editTitle" class="col-form-label">Title</label>
                            <input type="text" class="form-control" id="editTitle" name="title" spellcheck="false" />
                        </div>
                        <div class="flex-grow-1">
                            <label for="editURL" class="col-form-label">URL</label>
                            <input type="text" class="form-control" id="editURL" name="url" />
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>