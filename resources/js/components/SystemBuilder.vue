<template>
    <div v-show="error" class="error-banner">
        <i @click="error = null" class="fa-solid fa-close align-left"></i>
        {{ error }}
    </div>
    <div class="grid-container">
        <div>
            <table class="styled-table table-container">
                <thead>
                    <tr>
                        <th colspan="7" class="table-header">
                            <div class="dropdown-containers">
                                <high-low-dropdown :filter="filters.spread" :config="config.spread" filterName="Spread" @filter-changed="updateFilter"/>
                                <high-low-dropdown :filter="filters.week" :config="config.week" filterName="Week" @filter-changed="updateFilter"/>
                                <high-low-dropdown :filter="filters.total" :config="config.total" filterName="Total" @filter-changed="updateFilter"/>
                                <option-dropdown :filter="filters.divisional" :config="config.divisional" filterName="Divisional" @filter-changed="updateFilter"/>
                                <option-dropdown :filter="filters.homeaway" :config="config.homeaway" filterName="Home/Away" @filter-changed="updateFilter"/>
                                <option-dropdown :filter="filters.gametype" :config="config.gametype" filterName="Game Type" @filter-changed="updateFilter"/>
                                <high-low-dropdown :filter="filters.daysrest" :config="config.daysrest" filterName="Rest" @filter-changed="updateFilter"/>
                                <high-low-dropdown :filter="filters.opprest" :config="config.opprest" filterName="Opp Rest" @filter-changed="updateFilter"/>
                                <option-dropdown :filter="filters.lastresult" :config="config.lastresult" filterName="Last Result" @filter-changed="updateFilter"/>
                                <option-dropdown :filter="filters.lastlocation" :config="config.lastlocation" filterName="Last Location" @filter-changed="updateFilter"/>
                                <high-low-dropdown :filter="filters.prevresult" :config="config.prevresult" filterName="Prev Result" @filter-changed="updateFilter"/>
                                <option-dropdown :filter="filters.opplastresult" :config="config.opplastresult" filterName="Opp Last Result" @filter-changed="updateFilter"/>
                                <option-dropdown :filter="filters.opplastlocation" :config="config.opplastlocation" filterName="Opp Last Location" @filter-changed="updateFilter"/>
                                <high-low-dropdown :filter="filters.oppprevresult" :config="config.oppprevresult" filterName="Opp Prev Result" @filter-changed="updateFilter"/>
                            </div>
                            <div class="btn-container">
                                <button v-on:click="fetchSchedule()">UPDATE</button>
                                <button v-show="!openSave && !loadSave" v-on:click="openSave = true" class="success-btn">CREATE FILTER</button>
                                <button v-show="!openSave && !loadSave" v-on:click="loadSave = true" class="secondary-btn">LOAD FILTER</button>

                                <span v-show="openSave">
                                    <input type="text" v-model="filterName" placeholder="Filter Name" class="filter-name"/>
                                    <button v-on:click="saveCurrentFilter()" class="success-btn input-submit">SAVE</button>
                                    <button v-on:click="openSave = false" class="secondary-btn">CANCEL</button>
                                </span>

                                <span v-show="loadSave">
                                    <select class="filter-select" v-model="filterToLoad">
                                        <option v-for="filter in filterList" :key="filter.id" :value="filter.id">{{ filter.name }}</option>
                                    </select>
                                    <button v-on:click="loadFilter()" class="success-btn input-submit">LOAD</button>
                                    <button v-on:click="loadSave = false" class="secondary-btn">CANCEL</button>
                                </span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="table-body-scroll">
                    <tr v-for="game in schedule" :key="game.game_id">
                        <td>
                            <div class="bet-result">
                                <div v-if="game.bet_won" class="success">W</div>
                                <div v-else-if="game.bet_lost" class="failure">L</div>
                                <div v-else>-</div>
                                <div v-if="(game.team_score > 0 || game.opp_score > 0)">{{ game.team_score }} - {{ game.opp_score }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="bet-result">
                                <div>W{{ game.week }}</div>
                                <div>{{ game.season }}</div>
                            </div>
                        </td>
                        <td>
                            <img :src="game.team_logo" class="logo"/>
                        </td>
                        <td>
                            {{ game.team }} ({{ game.spread }})
                        </td>
                        <td>
                            {{ game.home ? "vs" : "@" }}
                        </td>
                        <td>
                            <img :src="game.opp_team_logo" class="logo"/>
                        </td>
                        <td>
                            {{ game.opp }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="results">
            <table class="units-table" v-if="record?.Total?.ATS">
                <thead>
                    <tr>
                        <th colspan="5">Grade: {{ record?.Total?.ATS?.Grade }}</th>
                    </tr>
                    <tr>
                        <th>Year</th>
                        <th>Wins</th>
                        <th>Losses</th>
                        <th>Units</th>
                        <th>ROI</th>
                    </tr>
                </thead>
                <tbody v-if="recordTableExpanded">
                    <tr v-for="(yearData, year) in record" :key="year">
                        <td>{{ year }}</td>
                        <td>{{ yearData.ATS.W }}</td>
                        <td>{{ yearData.ATS.L }}</td>
                        <td>{{ yearData.ATS.Units }}</td>
                        <td>{{ yearData.ATS.ROI }}</td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <td>Total</td>
                        <td>{{ record.Total.ATS.W }}</td>
                        <td>{{ record.Total.ATS.L }}</td>
                        <td>{{ record.Total.ATS.Units }}</td>
                        <td>{{ record.Total.ATS.ROI }}</td>
                    </tr>
                </tbody>
                <tr>
                    <td colspan="5" @click="recordTableExpanded = recordTableExpanded ? false : true" class="expand-yoy">
                        View Year-by-Year 
                        <i v-show="!recordTableExpanded" class="fa-solid fa-caret-right align-right"></i>
                        <i v-show="recordTableExpanded" class="fa-solid fa-caret-up align-right"></i>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</template>

<script>
import HighLowDropdown from './dropdowns/HighLowDropdown.vue';
import OptionDropdown from './dropdowns/OptionDropdown.vue';
export default {
  components: { HighLowDropdown, OptionDropdown },
    name: 'SystemBuilder',
    // other component properties...
    data() {
        return {
            schedule: [],
            record: [],
            selectedFilter: null,
            filterToLoad: null,
            recordTableExpanded: false,
            openSave: false,
            loadSave: false,
            filterName: null,
            error: null,
            filterList: [],
            filters: {
                spread: {low: -30,high: 30},
                week: {low: 1,high: 22},
                total: {low: 28,high: 64},
                daysrest: {low: 4,high: 17},
                opprest: {low: 4,high: 17},
                prevresult: {low:-52,high: 52},
                oppprevresult: {low:-52,high: 52},
                divisional: null,
                homeaway: null,
                gametype: null,
                lastresult: null,
                lastlocation: null,
                opplastresult: null,
                opplastlocation: null
            },
            config: {
                spread: {min: -30,max: 30},
                week: {min: 1,max: 22},
                total: {min: 28,max: 64},
                daysrest: {min: 4,max: 17},
                opprest: {min: 4,max: 17},
                prevresult: {min: -52,max: 52},
                oppprevresult: {min: -52,max: 52},
                divisional: {
                    options: {
                        "Yes": "Divisional",
                        "No": "Non-divisional"
                    }
                },
                homeaway: {
                    options: {
                        "home": "Home",
                        "away": "Away",
                        "neutral": "Neutral"
                    }
                },
                gametype: {
                    options: {
                        "regular": "Regular Season",
                        "playoffs": "Playoffs"
                    }
                },
                lastresult: {
                    options: {
                        "covered": "Covered Last",
                        "nocover": "No Cover Last"
                    }
                },
                lastlocation: {
                    options: {
                        "home": "Home Last",
                        "away": "Away Last"
                    }
                },
                opplastresult: {
                    options: {
                        "covered": "Opp Covered Last",
                        "nocover": "Opp No Cover Last"
                    }
                },
                opplastlocation: {
                    options: {
                        "home": "Opp Home Last",
                        "away": "Opp Away Last"
                    }
                }
            },
            translateArr: {
                'Spread':'spread',
                'Week':'week',
                'Total':'total',
                'Rest':'daysrest',
                'Opp Rest': 'opprest',
                'Divisional':'divisional',
                'Home/Away':'homeaway',
                'Game Type':'gametype',
                'Last Result': 'lastresult',
                'Last Location': 'lastlocation',
                'Prev Result': 'prevresult',
                'Opp Last Result': 'opplastresult',
                'Opp Last Location': 'opplastlocation',
                'Opp Prev Result': 'oppprevresult'
            }
        };
    },
    mounted() {
        this.fetchFilters();
        this.fetchSchedule();
    },
    methods: {
        fetchSchedule() {
            this.schedule = [];
            axios.get('/schedule/all', {
                params:{
                    filters: this.filters
                }
            })
            .then(response => {
                this.schedule = response.data.games;
                this.record = response.data.record;
            })
            .catch(error => {
                console.error('Error fetching schedule:', error);
            });
        },
        fetchFilters() { 
            axios.get('/filter/get')
            .then(response => {
                console.log(response);
                this.filterList = response.data.filters;
            })
            .catch(error => {
                console.error('Error fetching schedule:', error);
            });
        },
        slider: function() {
            if (this.filters.spread.low > this.filters.spread.high) {
                var tmp = this.filters.spread.high;
                this.filters.spread.high = this.filters.spread.low;
                this.filters.spread.low = tmp;
            }
        },
        updateFilter: function(newFilter, filterType) {
            console.log(filterType);
            let trueFilter = this.translateArr?.[filterType] ?? filterType;
            if (trueFilter) {
                this.filters[trueFilter] = newFilter;
            }
        },
        saveCurrentFilter: function () {
            const postData = {
                filters: this.filters,
                name: this.filterName
            }

            axios.post('/save/filter', postData)
            .then(response => {
                console.log(response);
                this.openSave = false;
                this.filterName = null;
            })
            .catch(error => {
                console.error('Error fetching schedule:', error);
            });
        },
        loadFilter: function () {
            axios.get('/filter/get/'+this.filterToLoad)
            .then(response => {
                this.filters = response.data.filters.filters;
                this.fetchSchedule();
                this.fetchFilters();
            })
            .catch(error => {
                console.error('Error fetching schedule:', error);
            });
            this.loadSave = false;
            this.filterToLoad = null;
        }
    },
}
</script>

<style>
    body {
        font-family: 'Lexend';
    }

    .table-container{
        max-height: 90vh; /* Adjust the maximum height as needed */
        height: 100vh;
        overflow-y: auto;
        display: block;
    }

    .table-header {
        padding: 5px;
    }
    .btn-container {
        margin-top: 15px;
    }

    .styled-table {
        border-collapse: collapse;
        border-spacing: 0;
        width: 100%;
    }

    /* Table header */
    .styled-table thead th {
        background-color: #edeff2;
        font-weight: bold;
        text-align: left;
        padding: 10px;
        border-bottom: 1px solid #ddd;
    }

    .styled-table thead {
        display: table;
        width: 100%;
        position: sticky;
        top: 0; /* or any other value that suits your layout */
        background-color: #f5f5f5; /* optional: add a background color for the sticky header */
        z-index: 100; /* optional: ensure the sticky header is above other content */
    }

    .styled-table tbody {
        display: table;
        width: 100%;
    }

    /* Table body - alternating row colors */
    .styled-table tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .styled-table tbody td {
        padding: 10px;
        border-bottom: 1px solid #ddd;
    }

    /* Highlight the table row on hover */
    .styled-table tbody tr:hover {
        background-color: #e0e0e0;
    }

    .grid-container {
        display: grid;
        grid-template-columns: repeat(2, 1fr); /* Two columns */
        gap: 10px; /* Adjust the gap between divs */
    }

    /* Units Table */
    .units-table {
        text-align:center;
    }
    .expand-yoy {
        cursor: pointer;
    }

    .dropdown-containers {
        display: grid;
        grid-template-columns: repeat(4, 1fr); /* Two columns */
        gap: 10px; /* Adjust the gap between divs */
    }

    .custom-dropdown {
        position: relative;
        display: inline-block;
        width: 100%;
        background-color: #FFFFFF;
        margin-right: 5px;
        margin-top: 5px;
        max-height: 38px;
    }

    /* Show options when the dropdown is expanded */
    .custom-dropdown.expanded .dropdown-options {
        display: block;
    }

    /* Additional styles for the custom dropdown */
    .dropdown-container {
        background-color: #FFFFFF;
        position: absolute;
        width:100%;
        top: 100%; /* Adjust this value as needed */
        left: 0;
        z-index: 999; /* Ensure it's above other content */
        /* Other styles for the dropdown container */
    }

    .dropdown-options {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 0px 0px 4px 4px;
    }

    .dropdown-filter-applied { 
        box-shadow: 5px 0 0 0 blue inset;
        border-radius: 4px 0 0 0;
    }

    .selected-value {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px 4px 0px 0px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .dropdown-options {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 0px 0px 4px 4px;
    }

    .logo {
        max-width: 50px;
        max-height: 50px;
    }

    .align-right {
        margin-left: auto;
    }

    .select-container {
        margin: 5px;
    }

    .bet-result { 
        width: 100%;
        height: 100%;
        text-align:center;
        margin:auto;
    }

    .success { 
        background-color: #4AAF41;
        color: white;
    }

    .failure { 
        background-color: #FF1612;
        color: white;
    }

    /* Style for the button */
    button {
        padding: 10px 20px;
        background-color: #3498db;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin-right: 10px;
    }

    /* Style when hovering over the button */
    button:hover {
        background-color: #2980b9;
    }

    .secondary-btn {
        background-color: gray;
    }

    .secondary-btn:hover {
        background-color: darkgray;
    }

    .success-btn {
        background-color: green;
    }

    .success-btn:hover {
        background-color: darkgreen;
    }

    .input-submit { 
        margin-left: 5px;
    }

    .filter-name {
        padding: 5px;
        width: 300px;
    }

    .error-banner {
        background-color: #f44336;
        color: white;
        padding: 10px;
    }

    .filter-select {
        padding:5px;
    }

    /* Style the dropdown content (hidden by default) */
    .dropdown-content {
        position: absolute;
        background-color: #fff;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    }

    .default-select {
        width: 100%;
        height: 20px;
    }

    .record-container { 
        display: flex;
        align-items: center;
    }

    .range-slider {
        max-width: 100%;
        text-align: center;
        position: relative;
        height: 10px;
        margin-right: 5px;
        margin-left: 5px;
    }

    .range-slider input[type=range] {
    position: absolute;
    left: 0;
    bottom: 0;
    }

    input[type=number] {
    border: 1px solid #ddd;
    text-align: center;
    font-size: 1.6em;
    }

    input[type=number]::-webkit-outer-spin-button,
    input[type=number]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    }

    input[type=number]:invalid,
    input[type=number]:out-of-range {
    border: 2px solid #ff6347;
    }

    input[type=range] {
    width: 100%;
    }

    input[type=range]:focus {
    outline: none;
    }

    input[type=range]:focus::-webkit-slider-runnable-track {
    background: #2497e3;
    }

    input[type=range]:focus::-ms-fill-lower {
    background: #2497e3;
    }

    input[type=range]:focus::-ms-fill-upper {
    background: #2497e3;
    }

    input[type=range]::-webkit-slider-runnable-track {
    width: 100%;
    height: 5px;
    cursor: pointer;
    background: #2497e3;
    border-radius: 1px;
    box-shadow: none;
    border: 0;
    }

    input[type=range]::-webkit-slider-thumb {
    z-index: 2;
    position: relative;
    box-shadow: 0px 0px 0px #000;
    border: 1px solid #2497e3;
    height: 18px;
    width: 18px;
    border-radius: 25px;
    background: #a1d0ff;
    cursor: pointer;
    -webkit-appearance: none;
    margin-top: -7px;
    }

    .roi-table {
        margin-top: 10px;
        border-collapse: collapse;
    }
    .roi-table th, 
    .roi-table td {
        border: 1px solid black;
        padding: 8px;
    }
    .roi-table th[colspan="2"], 
    .roi-table th[colspan="4"] {
        background-color: #f2f2f2;
    }
    
</style>