<!-- View Quotation Modal -->
<div class="modal fade" id="viewQuotationModal" tabindex="-1" role="dialog" aria-labelledby="viewQuotationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document" style="max-width: 90%;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewQuotationModalLabel">Quotation Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#viewQuotationModal').modal('hide')">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Quotation details will be loaded here via AJAX -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="$('#viewQuotationModal').modal('hide')">Close</button>
      </div>
    </div>
  </div>
</div>
