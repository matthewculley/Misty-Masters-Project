    @extends('layouts.app')

    @section('title', 'Stories')

    @section('content')
    <div id="root" class="container-fluid mx-auto border" style="width:90%;">
    
        <h1>Stories <a href="/add" class="btn btn-outline-primary"><h5>Add a new story</h5></a></h1>
        <div class="container">

            <div class="row">
                <div class="d-flex justify-content-left flex-wrap">
                    <div v-for="s in tags" :key="s.tag" class="form-check m-2">
                        <input class="form-check-input" :id="s.tag" type="checkbox" :value="s.tag" v-model="checkedTags"></input>
                        <label class="form-check-label" :for="s.tag"> @{{ s.tag }} </label>
                    </div>
                </div>
            </div>

            <div class="row d-flex justify-content-left flex-wrap p-0">

                <div class="col-sm-8 form-floating">
                    <input id="search" class="form-control" type="text" placeholder="Search by title" v-model="searchTerm"></input>
                    <label class="p-3"for="search"> Search</label>
                </div>

                <div class="col-sm-2 form-floating">
                    <select class="form-select" name="sortType" id="sortType" v-model="sortType">
                        <option value="alpha">Alphabetically</option>
                        <option value="age">Age</option>
                        <option value="lastPlayed">Last Played</option>
                        <option selected="selected" value="mostPlayed">Most Played</option>
                    </select>
                    <label class="p-3" for="sortType"> Sort by</label>
                </div>

                <div class="col-sm-2 form-floating">
                    <select class="form-select" name="orderBy" id="sortType" v-model="sortOrder">
                        <option value="asc">Ascending</option>
                        <option value="desc">Descending</option>
                    </select>
                    <label class="p-3"for="orderBy"> Order by</label>
                </div>   

            </div>

            <div class="row d-flex">
                <div class="col text-center">
                    <button type="submit" class="btn btn-primary m-2" @click="searchFilter" style="width:50%;">Search and filter</button>
                </div>
                <div class="col text-center">
                    <button type="submit" class="btn btn-primary m-2" @click="reset" style="width:50%;">Reset</button>
                </div>            
            </div>
            
        </div>

        <div class="d-flex justify-content-between flex-wrap">
            <div v-for="s in displayStories" :key="s.title" class="card m-1" style="width:250px">
                <img class="card-img-top" v-bind:src="s.thumbnail_path" style="width:100%">
                <div class="card-body">
                <h4 class="card-title">@{{ s.title }}</h4>
                <p class="card-text">@{{ s.description }}</p>
                <a :href="/stories/ + s.id" class="btn btn-primary">Select</a>
                </div>
            </div>
        </div>
</div>
    <script>
        var app = new Vue({
            el: "#root",
            data: {
                stories: [],
                displayStories: [],
                tags: [],
                searchTerm: "",
                checkedTags: [],
                returnData: [],
                sortType: "",
                sortOrder: "",
                history: [],
            },
            methods: {
                search: function() {
                    if (this.searchTerm == "") {
                        return;
                    }
                    this.displayStories = [];
                    var closeMatchesDict = {};
                    s1 = this.searchTerm.toLowerCase();
                
                    for(let i=0; i< this.stories.length; i++) {
                        var similarityIndex = 0.0;
                        var story = this.stories[i];
                        var s2 = story.title.toLowerCase();
                        var len1 = s1.length;
                        var len2 = s2.length;

                        // Maximum distance of shared characters allowed
                        var maxDist = Math.floor(Math.max(len1, len2) / 2) - 1;
                        var matches = 0;

                        var hash1 = Array(s1.length).fill(0);
                        var hash2 = Array(s2.length).fill(0);

                        for (let j=0; j<len1; j++) {
                        
                            for (var k = Math.max(0, j - maxDist); k < Math.min(len2, j + maxDist + 1); k++) {
                                if (s1[j] == s2[k] && hash2[k] == 0) {
                                    hash1[j] = 1;
                                    hash2[k] = 1;
                                    matches++;
                                    break;
                                }
                            }
                        }

                        var transpositions = 0;
                        var point = 0; 

                        for (let j=0; j<len1; j++) {
                            if (hash1[j]) {
                                while (hash2[point] == 0) {
                                    point++;
                                }
                                if (s1[j] != s2[point++]) {
                                    transpositions++;
                                }
                            }
                        }
                        transpositions /= 2;                    

                        if (s1 == s2) {
                            similarityIndex = 1.0;
                        } else if (matches == 0) {
                            similarityIndex = 0.0;
                        } else {
                            similarityIndex = ((matches) / (len1) 
                                        + (matches) / (len2) 
                                        + (matches - transpositions) / (matches)) 
                                        / 3.0;
                        }

                        if (similarityIndex > 0.7) {
                            var prefix = 0;
                            for (let j=0; j<Math.min(s1.length, s2.length); j++) {
                                if (s1[j] == s2[j]) {
                                    prefix++;
                                } else {
                                    break;
                                } 
                            }
                            prefix = Math.min(4, prefix);
                            similarityIndex += 0.1 * prefix * (1 - similarityIndex);
                        }
                        this.stories[i].similarity = similarityIndex;
                    }

                    for(let i=0; i<this.stories.length; i++) {
                        if (this.stories[i].similarity > 0.65) {
                            this.displayStories.push(this.stories[i]);
                        }
                    }

                    this.displayStories.sort((a, b) => {
                        return b.similarity - a.similarity;
                    });
                },

                sort: function() {
                    if (this.sortOrder === "" || this.sortType === "") {
                        return;
                    }
                    switch (this.sortType) {
                        case "age":
                            if (this.sortOrder == "asc") {
                                this.displayStories.sort((a, b) => {
                                    return b.max_suitable_age - a.max_suitable_age;
                                })
                            } else if (this.sortOrder == 'desc') {
                                this.displayStories.sort((a, b) => {
                                    return a.max_suitable_age - b.max_suitable_age;
                                })
                            }
                            break;
                        case "alpha":
                            if (this.sortOrder == "asc") {
                                this.displayStories.sort((a, b) => {
                                    return a.title.localeCompare(b.title);
                                })
                            } else if (this.sortOrder == 'desc') {
                                this.displayStories.sort((a, b) => {
                                    return b.title.localeCompare(a.title);
                                })
                            }
                            break;
                        case "lastPlayed":
                            if (this.sortOrder == "asc") {
                                this.displayStories.sort((a, b) => {
                                    return b.playOrder - a.playOrder;
                                })
                            } else if (this.sortOrder == 'desc') {
                                this.displayStories.sort((a, b) => {
                                    return a.playOrder - b.playOrder;
                                })
                            }
                            break;
                        case "mostPlayed": 
                            if (this.sortOrder == "asc") {
                                this.displayStories.sort((a, b) => {
                                    return a.times_played - b.times_played;
                                })
                            } else if (this.sortOrder == 'desc') {
                                this.displayStories.sort((a, b) => {
                                    return b.times_played - a.times_played;
                                })
                            }
                            break;
                    }           
                },
                
                filterTags: function() {
                    if (this.checkedTags == []) {
                        return;
                    }

                    var tags = String(this.checkedTags).replace(/,/g, "+").replace(/ /g, "_");

                    if (!tags == "") {
                        //get all stories with those tags
                        axios.get("/api/stories/tags/" + tags)
                        .then(response=>{
                            console.log(response.response);
                            var validStories = [];
                            for (let i=0; i<response.data.length; i++) {
                                var inDisplayStories = false;
                                var storyId = response.data[i].id;
                                for (let j=0; j<this.displayStories.length; j++) {
                                    displayStoryId = this.displayStories[j].id;
                                    if (storyId == displayStoryId) {
                                        inDisplayStories = true;
                                        validStories.push(response.data[i]);
                                        break;
                                    }
                                }                            
                            }
                            this.displayStories = (validStories);
                            // this.sort();
                            // this.displayStories = (response.data);
                            
                        })
                        .catch(response => {
                            this.returnData = [];
                            console.log(response.data);
                            console.log(response.response);
                        })
                    }
                },

                searchFilter: function() {
                    var tags = this.checkedTags;
                    var search = this.searchTerm;
                    var order = this.sortOrder;
                    var type = this.sortType;

                    this.reset();

                    this.checkedTags = tags;
                    this.searchTerm = search;
                    this.sortType = type;
                    this.sortOrder = order;
                    this.search();
                    this.filterTags();
                    this.sort()
                },

                reset: function() {
                    this.displayStories = this.stories;
                    this.checkedTags = [];
                    this.searchTerm = "";
                    this.sortOrder = "asc";
                    this.sortType = "alpha";
                    this.sort();
                }
            },
            mounted() {
                
                axios.get("/api/stories")
                .then(response=>{
                    this.stories = Object.values(response.data);
                    console.log(response);
                    this.reset();
                    this.sort();

                })
                .catch(response => {
                    console.log(response.data);
                }),

                axios.get("/api/tags")
                .then(response=>{
                    this.tags = Object.values(response.data);
                    console.log(response);
                })
                .catch(response => {
                    console.log(response.data);
                }),

                axios.get("/api/misty/stories_played/")
                .then(response=>{
                    this.history = (response.data);

                    this.history.sort((a, b) => {
                        return new Date(a.lastPlayed) - new Date(b.lastPlayed);
                    });

                    this.stories.unshift([]);
                    //for each story, find when most recently played
                    for (let i=1; i<this.stories.length    ; i++) {
                        //find the first index in histories that has the same id
                        var firstIndex = -1;
                        var found = false;

                        for (let j=0; j<this.history.length; j++) {
                            if (found == true) {
                                break;
                            }
                            if (this.history[j].id == i) {
                                found = true; 
                                firstIndex = j;
                            }
                        }
                        if (found) {
                            this.stories[i].playOrder = firstIndex;
                        } else {
                            this.stories[i].playOrder = Number.MAX_SAFE_INTEGER;
                        }
                    }
                    
                    this.stories.shift([]);
                    this.displayStories = this.stories;
                })
                .catch(response => {
                    console.log(response.response);
                })
            
            }   
        });            
    </script>
    @endsection

   