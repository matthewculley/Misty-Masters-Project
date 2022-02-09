@extends('layouts.app')

@section('title', 'Stories')

@section('content')
<div id="root">
    <h3>Stories</h3>
    <div id="search">
        <h5>Search by title</h5>
        <input type="text" placeholder="search" v-model="searchTerm"></input>
        <input type="submit" value="Search" @click="search"></input>
        <br>
    </div>
    <div id="keywords">
        <h5>Or by keywords</h5>
        <div v-for="s in tags">
            <input :id="s.tag" type="checkbox" :value="s.tag" v-model="checkedTags"></input>
            <label :for="s.tag">@{{ s.tag }}</label>
        </div>
        <input type="submit" value="Apply Keywords" @click="filterTags"></input>
    </div>
    <div id="sort">
        <h5>Sort</h5>
        <label for="cars">Sort by: </label>
        <select name="sortType" id="sortType" v-model="sortType">
            <option value="" disabled selected>Sort by</option>
            <option value="alpha">Alphabetically</option>
            <option value="age">Age</option>
            <option value="lastPlayed">Last Played</option>
            <option selected="selected" value="mostPlayed">Most Played</option>
        </select>
        <select name="" id="sortType" v-model="sortOrder">
        <option value="" disabled selected>Order by</option>
            <option value="asc">Ascending</option>
            <option value="desc">Descending</option>
        </select>
        <input type="submit" value="Sort" @click="sort"></input>
    </div>
    <div id="reset">
        <h5>Reset search and keywords</h5>
        <input type="submit" value="Reset" @click="reset"></input>
    </div>
    <div id="results">
        <ul>
            <li v-for="s in displayStories" :key="s.title"><a :href="/stories/ + s.id">@{{ s.title }}</a></li> 
        </ul>
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
                this.reset();
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
                                return b.times_played - a.times_played;
                            })
                        } else if (this.sortOrder == 'desc') {
                            this.displayStories.sort((a, b) => {
                                return a.times_played - b.times_played;
                            })
                        }
                    break;
                }           
            },
            
            filterTags: function() {
                this.reset();
                var tags = String(this.checkedTags).replace(/,/g, "+").replace(/ /g, "_");
                if (!tags == "") {
                    axios.get("/api/stories/tags/" + tags)
                    .then(response=>{
                        this.displayStories = (response.data);
                        console.log(response.data);
                    })
                    .catch(response => {
                        this.returnData = [];
                        console.log(response.data);
                        console.log(response.response);
                    })
                } else {
                    this.reset();
                }
                
            },

            reset: function() {
                this.displayStories = this.stories;
            }
        },
        mounted() {
            
            axios.get("/api/stories")
            .then(response=>{
                this.stories = Object.values(response.data);
            })
            .catch(response => {
                console.log(response.data);
            }),

            axios.get("/api/tags")
            .then(response=>{
                this.tags = Object.values(response.data);
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

                //for each story, find when most recently played
                for(let i=1; i<=this.stories.length; i++) {
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
                        this.stories[i-1].playOrder = firstIndex;
                    } else {
                        this.stories[i-1].playOrder = Number.MAX_SAFE_INTEGER;
                    }
                }

                this.displayStories = this.stories;
            })
            .catch(response => {
                console.log(response.response);
            })
        
        }   
    });            
</script>
@endsection