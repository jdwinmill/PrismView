<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="_token" content="{{csrf_token()}}" />
    <title>PrismView Creative Engine</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.1/css/bulma.min.css">
    <script defer src="https://use.fontawesome.com/releases/v5.1.0/js/all.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.5.17/dist/vue.js"></script>
    <style>

        .template-container{
            width: 300px;
            float: left;
            margin: 10px;
            border: 1px solid #cacaca;
        }
        .template-info{
            margin:5px;
            padding:5px;
        }

        .filterButton{
            margin: 5px;
        }
        select{
            width:100%;
        }
        .title{
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container is-fluid">
    <h1 class="title">Creative Engine Template</h1>



    <div id="app">
        <div class="columns">
            <div class="column is-one-fifth">
                <h3 class="subtitle">Create New Template</h3>
                <form id="newTemplateForm" @submit.prevent="handleSubmit">
                    <label>Name</label>
                    <input class="input is-rounded" type="text" id="templateTitle"  name="templateTitle" value="" v-model="newTemplate.title">
                    <br><br>
                    <label>Description</label>
                    <input class="input is-rounded" type="text" id="templateDescription"  name="templateDescription" value="" v-model="newTemplate.description">
                    <br><br>
                    <label>Category</label><br>

                    <select class="select is-multiple" v-model="newTemplate.categories" multiple size="8">
                        <option v-for="filter in filterCategories" v-bind:value="filter.id" multiple size="8">@{{ filter.name }}</option>
                    </select>

                    <br><br>
                    <button class="button is-rounded is-info" >Create</button>
                </form>
                <hr>
                <br>
                <div>
                    <h3 class="subtitle">Filter Categories</h3>
                    <span v-for="category in filterCategories">
                                <button class="button is-rounded is-small filterButton" v-bind:id="category.name"  @click="categoryFilterController(category.id, category.name)">@{{ category.name }}</button>
                            </span>
                </div>


            </div>

            <div class="column is-two-thirds">
                <div class="container">
                    <div class="template-container" v-for="template in templates">
                        <div class=\"img\">
                            <img src="https://bulma.io/images/placeholders/640x480.png">
                        </div>
                        <div class="template-info">
                            <p>@{{ template.title }}</p><br>
                            <button class="button is-info"> Customize</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>




    </div>
</div>



<script>


    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });

    new Vue({
        el: '#app',
        data: {
            templates: [],
            filterCategories: [],
            selectedCategories: [],
            categoryFilter: [],
            newTemplate: {
                'title': '',
                'description': '',
                'categories': []
            }
        },
        mounted: function () {
            this.getAllTemplates();
            this.getAllCategories();
        },
        methods: {

            /**
             * Handle form data
             */
            handleSubmit() {
                let title = this.newTemplate.title;
                let description = this.newTemplate.description;
                let categories = JSON.stringify(this.newTemplate.categories);

                this.createNewTemplate(title, description, categories);
                this.toggleTemplates();

                // clear all fields in form
                this.newTemplate.title = '';
                this.newTemplate.description = '';
                this.newTemplate.categories = [];

            },

            /**
             * toggle button to inform user of selected category
             * toggle filter list
             * toggle templates
             *
             * @param {int} categoryID
             * @param {string} categoryName
             */
            categoryFilterController: function (categoryID, categoryName) {
                // toggle button status

                this.toggleButtonStatus(categoryName);
                this.toggleCategoryIdFromFilterList(categoryID);
                this.toggleTemplates();
            },

            /**
             * check if id exists
             * delete if id exists
             * push if id doesnt exist
             *
             * @param {int} categoryID
             */
            toggleCategoryIdFromFilterList: function (categoryID) {
                // if category id is in categoryFilter array
                if(this.categoryFilter.includes(categoryID)) {
                    // find the index of element
                    let index = this.categoryFilter.indexOf(categoryID);

                    // remove the element by its index
                    if (index > -1) {
                        this.categoryFilter.splice(index, 1);
                    }

                } else {
                    // add category ID to array
                    this.categoryFilter.push(categoryID);
                }
            },

            /**
             * Toggle templates
             * gets all categories
             * or
             * get categories by ids
             *
             */
            toggleTemplates: function () {
                // if empty
                if(this.isEmpty(this.categoryFilter)){
                    this.getAllTemplates();
                } else {
                    // else -- if not empty
                    this.getTemplatesByCategoryIds();
                }
            },

            /**
             * gets all template data from db via axios request
             *
             */
            getAllTemplates: function () {
                //let url = '/api/templates';
                let url = '/templates';

                axios.get(url).then(response => {
                    this.templates = response.data
                })
            },

            /**
             * gets all categories data from db via axios request
             *
             */
            getAllCategories: function () {
                //let url = '/api/categories';
                let url = '/categories';

                axios.get(url).then(response => {
                    this.filterCategories = response.data
                })
            },

            /**
             * create a new template
             *
             * @param {string} title
             * @param {string} description
             * @param {string} categories
             */
            createNewTemplate: function (title, description, categories) {

                axios.post('/templates',{title: title, description: description, categories: categories})
                    .then(function (response) {
                        return response;
                    })
            },

            /**
             * filter templates by category ids
             *
             */
            getTemplatesByCategoryIds: function () {
                let url = '/templates/byCategories/'+ JSON.stringify(this.categoryFilter);

                axios.get(url).then(response => {
                    this.templates = response.data
                })
            },

            /**
             * toggle button by id
             *
             * @param {string} idName
             */
            toggleButtonStatus: function (idName) {
                let buttonElement = document.getElementById(idName);
                buttonElement.classList.toggle("is-success");
            },

            /**
             * check if object is empty
             *
             * @param {string} obj
             */
            isEmpty: function (obj) {
                for(var key in obj) {
                    if(obj.hasOwnProperty(key))
                        return false;
                }
                return true;
            }
        }
    })

</script>
</body>
</html>

