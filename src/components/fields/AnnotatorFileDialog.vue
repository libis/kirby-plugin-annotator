<template>
  <div v-if="open" class="k-dialog-overlay">
    <div class="k-dialog k-models-dialog">
      <div class="k-dialog-body">
        <div class="k-models-section-search k-input">
          <k-search-input :value="searchValue" @input="searchValue = $event" />
          <span class="k-input-icon" @click="search">
            <k-icon type="search" />
          </span>
        </div>

        <k-collection v-if="items" :items="items" :pagination="pagination" @paginate="onPaginate"
          :empty="emptyCollection" class="k-dialog-item-collection">
          <template #options="{ item: row }">
            <k-choice-input type="checkbox" :checked="isSelected(row.id)"
              :title="isSelected(row.id) ? $t('remove') : $t('select')" @input="toggle(row)"  />
          </template>
        </k-collection>
      </div>

      <div class="k-dialog-footer">
        <div class="k-button-group k-dialog-buttons">
          <k-button icon="cancel" variant="filled" @click="$emit('close')">{{ $t("libis.annotator.cancel") }}</k-button>
          <k-button icon="check" variant="filled" theme="green" @click="confirm">{{ $t("libis.annotator.confirm") }}</k-button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "k-annotator-file-dialog",
  props: {
    open: Boolean,
    values: Array,
    query: {
      type: String,
      default: () => ""
    },
    limit: Number
  },
  data() {
    return {
      emptyCollection: { text: "Empty" },
      selected: (this.values || []).map(value =>
        typeof value === 'string'
          ? { id: value } 
          : value
      ),
      searchValue: "",
      pagination: {
        page: 1,
        pages: 1,
        limit: this.limit ?? 0,
        total: 0
      },
      items: [],
      allItems: [],
      cachedPages: {},
      cachedSearchItem: "",
      searchTimeout: null
    };
  },
  created() {
    this.fetchItems();
  },
  mounted() {
    const inDrawer = this.$el.closest('.k-drawer');
    if (!inDrawer) {
      const panelRoot = document.querySelector('.k-panel');
      if (panelRoot) {
        panelRoot.appendChild(this.$el);
      }
    }
  },
  watch: {
    searchValue(newValue) {
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => {
        this.fetchItems(newValue);
      }, 300);
    }
  },
  methods: {
    isSelected(id) {
      return this.selected.some(item => item.id === id);
    },
    // handle select / deselect
    toggle(row) {
      const exists = this.selected.find(item => item.id === row.id);
      if (exists) {
        this.selected = this.selected.filter(item => item.id !== row.id);
      } 
      else {
        this.selected.push({ id: row.id, text: row.text, info: row.info, image: row.image });
      }
    },
    confirm() {
      this.$emit("confirm", this.selected.map(item => item.id));
    },
    // get all the files if quey excist use it otherwise takes page images
    async fetchItems(q = "", page = 1) {
      if (this.cachedPages[page] && this.cachedSearchItem == q) {
        this.items = this.cachedPages[page];
        this.pagination.page = page;
        this.cacheNextItems(page);
        this.cachePreviousItems(page);
        return;
      }

      this.cachedSearchItem = q;

      const searchquery = q !== "" ? "q=" + q + "&" : "";
      let url = `/annotator/files?${searchquery}query=${this.query}&page=${page}&limit=${this.pagination.limit}`;
      const response = await fetch(
        url
      );

      const data = await response.json();
      const mapped = data.files.map(item => ({
        text: item.filename,
        info: item.parent,
        image: item.panel?.image || item.url,
        id: item.uuid,
      }));

      this.cachedPages[page] = mapped;

      this.items = mapped;
      this.pagination.total = data.total;
      this.pagination.pages = Math.ceil(data.total / this.pagination.limit);
      this.pagination.page = page;

      this.cacheNextItems(page);
      this.cachePreviousItems(page);
    },
    async cacheNextItems(page) {
      const nextPage = page + 1;
      if (nextPage <= this.pagination.pages && !this.cachedPages[nextPage]) {
        fetch(`/annotator/files?query=${this.query}&page=${nextPage}&limit=${this.pagination.limit}`)
          .then(r => r.json())
          .then(data => {
            this.cachedPages[nextPage] = data.files.map(item => ({
              text: item.filename,
              info: item.parent,
              image: item.panel?.image || item.url,
              id: item.uuid,
            }));
          });
      }
    },
    async cachePreviousItems(page) {
      const previousPage = page - 1;
      if (previousPage > 0 && !this.cachedPages[previousPage]) {
        fetch(`/annotator/files?query=${this.query}&page=${previousPage}&limit=${this.pagination.limit}`)
          .then(r => r.json())
          .then(data => {
            this.cachedPages[previousPage] = data.files.map(item => ({
              text: item.filename,
              info: item.parent,
              image: item.panel?.image || item.url,
              id: item.uuid,
            }));
          });
      }
    },
    search() {
      this.fetchItems(this.searchValue);
    },
    // get files of asked page 
    setPage(page) {
      this.fetchItems(this.searchValue, page);
    },
    onPaginate(page) {
      this.setPage(page.page);
    }
  }
};
</script>

<style>
.k-dialog-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 9999;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
}

.buttons {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1rem;
}

.k-collection.k-dialog-item-collection .k-item .k-item-content {
  width: 90%;
}
</style>