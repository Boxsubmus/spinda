import EasyMDE from 'easymde';
import 'easymde/dist/easymde.min.css';

const el = document.getElementById('about-me-textarea');
if (el) {
    new EasyMDE({
        element: el,
        spellChecker: false,
        status: false,
        toolbar: ['bold', 'italic', 'heading', '|', 'unordered-list', 'ordered-list', 'link', '|', 'preview'],
    });
}