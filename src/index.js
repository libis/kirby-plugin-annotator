import AnnotatorField from "./components/fields/Annotator.vue";
import PinIcon from "./components/icons/pin.vue";
import FileField from "./components/fields/AnnotatorFile.vue";
import FileDialogField from "./components/fields/AnnotatorFileDialog.vue";
import ImageAnnotator from "./components/fields/ImageAnnotator.vue";
import MarkerTable from "./components/fields/MarkerTable.vue";
import TemplateSelect from "./components/fields/TemplateSelect.vue";

panel.plugin('libis/annotator', {
  fields: {
    annotator: AnnotatorField,
    annotatorFile: FileField,
  },
  components: {
    "k-pin-icon": PinIcon,
    "k-annotator-file": FileField,
    "k-annotator-file-dialog": FileDialogField,
    "k-image-annotator": ImageAnnotator,
    "k-marker-table": MarkerTable,
    "k-template-select": TemplateSelect
  },
});