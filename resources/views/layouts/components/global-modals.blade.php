<div class="modal fade" style="z-index: 1060;" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel"
    aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered py-0 px-4">
        <div class="modal-content bg-white rounded-2 px-2">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalTitle">Are you sure to
                    delete this item?</h1>
            </div>
            <div class="modal-body rounded-1 bg-white">
                <div class="delete-text">
                    <p id="modalDescription" class="m-0 p-0 mt-1 fw-bold fs-6"
                        style="letter-spacing: 0.8px; color: rgb(110, 110, 110);">This action cannot be undone!</p>
                </div>
            </div>
            <form id="deleteForm" method="post" class="modal-footer justify-content-between border-0 ">
                @csrf
                @method('DELETE')
                <button type="button" id="cancelBtn" class="btn btn-sm btn-primary p-3 py-2 shadow fs-7 fw-bold" data-bs-dismiss="modal"
                    aria-label="Close">Cancel</button>
                <button type="submit" id="confirmBtn" class="btn btn-sm btn-danger text-decoration-none p-3 py-2 shadow fs-7 fw-bold">Delete</button>
            </form>
        </div>
    </div>
</div>