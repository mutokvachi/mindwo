<div id='drop_{{ $frm_uniq_id }}' class="dropzone"></div>
<script>
    $("div#drop_{{ $frm_uniq_id }}").dropzone({
        paramName: "{{ $item_field }}",
        maxFiles: {{ ini_get('max_file_uploads') }},
        url: "/file/post", 
        dictDefaultMessage: "<h3>Ievelciet šeit datnes</h3><p>Vai arī spiediet ar peli, lai atvērtu datņu pārlūkošanas logu.</p>",
        dictMaxFilesExceeded: "Vienā saglabāšanas reizē pieļaujams vienlaicīgi augšuplādēt ne vairāk kā {{ ini_get('max_file_uploads') }} datnes!",
        init:   function() {
                    this.on("addedfile", function(file) {
                        // Create the remove button
                        var removeButton = Dropzone.createElement("<a href='javascript:;'' class='btn btn-sm btn-default btn-block' style='margin-top: 10px; cursor: hand;'>Noņemt</a>");
                        
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
                    });
                } 
    });
</script>