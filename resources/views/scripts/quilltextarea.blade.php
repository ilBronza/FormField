
@section('quillTextarea')

<link href="https://cdn.quilljs.com/1.0.0/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.0.0/quill.js"></script>


<script src="https://cdn.ckeditor.com/ckeditor5/33.0.0/decoupled-document/ckeditor.js"></script>

<script type="text/javascript">

function quilljs_textarea(elem = null, options = null) {
    if(elem) {
        var editorElems = Array.prototype.slice.call(document.querySelectorAll(elem));
    } else {
        var editorElems = Array.prototype.slice.call(document.querySelectorAll('[data-quilljs]'));
    }
    editorElems.forEach(function(el) {
    if(elem && el.hasAttribute("data-quilljs")) {
        return;
    }
    var elemType = el.type;
    if(elemType == 'textarea') {
        elemValue = el.value;
        editorDiv = document.createElement('div');
        editorDiv.innerHTML = elemValue;
        el.parentNode.insertBefore(editorDiv, el.nextSibling);
        el.style.display = "none";
        var placeholder = el.placeholder;
    } else {
        var placeholder = null;
        editorDiv = el;   
    }
    if(!options) {
        var default_options = {
        theme: 'snow',
        placeholder: placeholder,
        };
    } else {
        if(!options.placeholder) {
        options.placeholder = placeholder;
        }
        var default_options = options;
    }

    var editor = new Quill(editorDiv, default_options);
    editor.on('text-change', function(delta, oldDelta, source) {
        var editor_value = editor.root.innerHTML;
        el.value = editor_value;
    });
    });
}
(function() {
    quilljs_textarea();
})();
</script>

<script>
jQuery(document).ready(function($)
{
    (function()
    {
        quilljs_textarea('textarea.texteditor',
        {
            modules: { toolbar: [
                ['bold', 'italic', 'underline'],        // toggled buttons
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
                [{ 'align': [] }],
            ]},
            theme: 'snow',
        });
    })();
});

</script>

@endsection

