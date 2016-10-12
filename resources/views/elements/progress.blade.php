<!-- Progres window -->
<div class='modal fade' id='progres_window' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true' data-backdrop="static" style="z-index: 999999;">
  <div class='modal-dialog'>
    <div class='modal-content'>
      <div class='modal-header'>	        
        <h4 class='modal-title' id='progres_label'>DX</h4>
      </div>
      <div class='modal-body' id="progres_body">
          <img src="{{Request::root()}}/img/loading.gif" alt="Lūdzu, uzgaidiet..." title="Lūdzu, uzgaidiet..." /> Notiek datu apstrāde... Lūdzu, uzgaidiet...
      </div>
      <div class='modal-footer' id="progres_footer">
        <button type='button' class='btn btn-default' data-dismiss='modal' id = "progres_button">Atcelt</button>
      </div>
    </div>
  </div>
</div>
<script>
    function show_progres_result(title, msg, button_title)
    {
            $("#progres_label").html(title);
            $("#progres_body").html(msg);

            if (button_title.length > 0)
            {
                    $("#progres_button").html(button_title);
                    $("#progres_footer").show();
            }
            else
            {
                    $("#progres_footer").hide();
            }

    }
</script>