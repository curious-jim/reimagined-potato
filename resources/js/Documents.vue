<template>
    <div>
        <input type="file" name="document" ref="file" required accept="application/pdf" @change="fileSelected"/>
        <input type="button" class="btn btn-primary" value="Upload" @click="uploadDocument" :disabled="! file"/>

        <div v-if="documents.length">

            <div class="modal" tabindex="-1" v-if="pdf" style="display: block">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Document</h5>
                            <button
                                type="button"
                                class="close"
                                data-dismiss="modal"
                                aria-label="Close"
                                @click="closeModal"
                            >
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="frame-container">
                                <iframe :src="pdf" frameborder="0"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h3>Documents</h3>

            <div class="row">
                <div class="col-3" v-for="(document, key) in documents" :key="key">
                    <a href="#" @click="showDocument(document)">
                        <img :src="document.thumbnail" class="img-thumbnail" :alt="document.name">
                    </a>
                </div>
            </div>

            <nav>
                <ul class="pagination">
                    <li :class="'page-item' + (prev_page_url ? '' : ' disabled')"><a class="page-link" href="#" @click="loadDocuments(prev_page_url)">Previous</a></li>
                    <li :class="'page-item' + (next_page_url ? '' : ' disabled')"><a class="page-link" href="#" @click="loadDocuments(next_page_url)">Next</a></li>
                </ul>
            </nav>
        </div>
    </div>
</template>

<script>
export default {
    props: ['routeIndex', 'routeUpload'],
    data() {
        return {
            file: null,
            documents: [],
            prev_page_url: null,
            next_page_url: null,
            pdf: false
        }
    },
    methods: {
        loadDocuments(url) {
            axios.get(url)
                .then(res => {
                    this.documents = res.data.data;
                    this.prev_page_url = res.data.prev_page_url;
                    this.next_page_url = res.data.next_page_url;
                });
        },
        fileSelected() {
            this.file = this.$refs.file.files[0];
            this.canUpload = true;
        },
        uploadDocument() {
            let formData = new FormData();
            formData.append('document', this.file);
            let self = this;

            axios.post(this.routeUpload,
                formData,
                {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }
            ).then(res => {
                self.$set(self.documents, self.documents.length, res.data);
                this.fileReset();
            })
            .catch(err => {
                //simple
                alert(err.response.data.message || err.message);
                this.fileReset();
            });

            return false;
        },
        showDocument(document) {
            this.pdf = document.pdf;

            // axios.get(url)
            //     .then(res => {
            //         self.pdf = res.data.pdf;
            //     });
            return false;
        },
        closeModal() {
            this.pdf = false;
        },
        fileReset() {
            this.$refs.file.value = null;
            this.file = false;
        }
    },
    created() {
        this.loadDocuments(this.routeIndex);
    }
}
</script>

<style scoped>
.modal-dialog {
    width: 100%;
    height: 100%;
    margin: 0;
    padding: 0;
    max-width: 100%;
}

.modal-content {
    height: auto;
    min-height: 100%;
    border-radius: 0;
}

.frame-container {
    position: relative;
    overflow: hidden;
    padding-top: 70%;
}
.frame-container iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: 0;
}
</style>
