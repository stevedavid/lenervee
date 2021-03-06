{% for (var i=0, file; file=o.files[i]; i++) { %}
<tr class="template-upload fade">
    <td>
    <span class="preview"></span>
    </td>
    <td>
    <p class="name">{%=file.name%}</p>
<strong class="error text-danger label label-danger"></strong>
    </td>
    <td>
    <p class="size">Processing...</p>
    <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
    <div class="progress-bar progress-bar-success" style="width:0%;"></div>
    </div>
    </td>
    <td> {% if (!i && !o.options.autoUpload) { %}
<button class="btn blue start" disabled>
<i class="fa fa-upload"></i>
    <span>Start</span>
    </button> {% } %} {% if (!i) { %}
<button class="btn red cancel">
        <i class="fa fa-ban"></i>
        <span>Cancel</span>
        </button> {% } %} </td>
</tr> {% } %}