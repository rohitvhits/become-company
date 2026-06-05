<link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.1.0/ckeditor5.css">
<script type="importmap">
    {
        "imports": {
            "ckeditor5": "https://cdn.ckeditor.com/ckeditor5/43.1.0/ckeditor5.js",
            "ckeditor5/": "https://cdn.ckeditor.com/ckeditor5/43.1.0/"
        }
    }
</script>

<script type="module">
    import {
        ClassicEditor,
        Essentials,
        Paragraph,
        Bold,
        Italic,
        Font,
        List
    } from 'ckeditor5';

    // Description editor
    ClassicEditor
        .create( document.querySelector( '#description' ), {
            plugins: [ Essentials, Paragraph, Bold, Italic, Font, List ],
            toolbar: [
                'undo', 'redo', '|', 'bold', 'italic', '|',
                'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', '|',
                'bulletedList', 'numberedList'
            ]
        } )
        .then( editor => {
            window.descriptionEditor = editor;
           // editor.ui.view.editable.element.style.minHeight = '150px';
        } )
        .catch( error => {
            console.error( error );
        } );

    // Summary editor
    ClassicEditor
        .create( document.querySelector( '#steps_summary' ), {
            plugins: [ Essentials, Paragraph, Bold, Italic, Font, List ],
            toolbar: [
                'undo', 'redo', '|', 'bold', 'italic', '|',
                'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', '|',
                'bulletedList', 'numberedList'
            ]
        } )
        .then( editor => {
            window.stepsSummaryEditor = editor;
           // editor.ui.view.editable.element.style.minHeight = '150px';
        } )
        .catch( error => {
            console.error( error );
        } );

    // Edit Description editor
    ClassicEditor
        .create( document.querySelector( '#edit_description' ), {
            plugins: [ Essentials, Paragraph, Bold, Italic, Font, List ],
            toolbar: [
                'undo', 'redo', '|', 'bold', 'italic', '|',
                'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', '|',
                'bulletedList', 'numberedList'
            ]
        } )
        .then( editor => {
            window.editDescriptionEditor = editor;
           // editor.ui.view.editable.element.style.minHeight = '150px';
        } )
        .catch( error => {
            console.error( error );
        } );

    // Edit Summary editor
    ClassicEditor
        .create( document.querySelector( '#edit_steps_summary' ), {
            plugins: [ Essentials, Paragraph, Bold, Italic, Font, List ],
            toolbar: [
                'undo', 'redo', '|', 'bold', 'italic', '|',
                'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', '|',
                'bulletedList', 'numberedList'
            ]
        } )
        .then( editor => {
            window.editStepsSummaryEditor = editor;
           // editor.ui.view.editable.element.style.minHeight = '150px';
        } )
        .catch( error => {
            console.error( error );
        } );
</script>
