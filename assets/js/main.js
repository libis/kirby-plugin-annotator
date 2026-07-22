import { annotatorBlock } from '../js/annotator.js';

document.addEventListener('DOMContentLoaded', () => {
  const annotators = document.querySelectorAll('.annotator-field-section-wrapper');
  annotators.forEach(annotator => {
    annotatorBlock(annotator);
  });
});