<div id='drop_{{ $frm_uniq_id }}' class="dropzone"></div>
<script>
    $("div#drop_{{ $frm_uniq_id }}").dropzone({
        paramName: "{{ $item_field }}",
        maxFiles: {{ ini_get('max_file_uploads') }},
        url: "fakepath", 
        autoProcessQueue: false,
        dictDefaultMessage: "<h3>" + Lang.get('fields.title_drag_drop') + "</h3><p>" + Lang.get('fields.title_mouse_click') + "</p>",
        dictMaxFilesExceeded: Lang.get('fields.rule_part1') + "{{ ini_get('max_file_uploads') }}" +  Lang.get('fields.rule_part2'),
        init:   function() {
                    this.on("addedfile", function(file) {
                        // Create the remove button
                        var removeButton = Dropzone.createElement("<a href='javascript:;' class='btn btn-sm btn-primary btn-block' style='margin-top: 10px; cursor: pointer;'>" + Lang.get('fields.btn_remove') + "</a>");
                        
                        // Capture the Dropzone instance as closure.
                        var _this = this;

                        // Listen to the click event
                        removeButton.addEventListener("click", function(e) {
                          // Make sure the button click doesn't submit the form:
                          e.preventDefault();
                          e.stopPropagation();

                          // Remove the file preview.
                          _this.removeFile(file);
                          // If you want to the delete the file on the server as well,
                          // you can do the AJAX request here.
                        });

                        // Add the button to the file preview element.
                        file.previewElement.appendChild(removeButton);
                        
                        $(".dz-progress").remove();
                        $('.dz-preview').addClass('dz-complete');
                        $(".dz-error-message").css('margin-top', '35px');
                        $(".dz-error-message").css('color', 'white');
                    });
                } 
    });
</script>