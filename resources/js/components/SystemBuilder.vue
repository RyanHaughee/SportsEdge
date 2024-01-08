<template>
    <div class="table-container">
        <table class="styled-table">
            <thead>
                <tr>
                    <th colspan="7">
                        <div class="custom-dropdown" :class="{ 'dropdown-filter-applied': filters.spread.low > -30 || filters.spread.high < 30 }">

                            <div class="selected-value" @click="selectedFilter = (selectedFilter == 'spread' ? null : 'spread')">
                                <span v-if="!(filters.spread.low > -30 || filters.spread.high < 30)">Spread</span>
                                <span v-else>{{ filters.spread.low ? filters.spread.low : "less than" }} {{ (filters.spread.high && filters.spread.low) ? "to" : "" }} {{ filters.spread.high ? filters.spread.high : "or more" }}</span>
                                <i v-show="selectedFilter != 'spread'" class="fa-solid fa-caret-right align-right"></i>
                                <i v-show="selectedFilter == 'spread'" class="fa-solid fa-caret-down align-right"></i>
                            </div>
                            <div class="dropdown-container" v-show="selectedFilter == 'spread'">
                                <div class="dropdown-options">
                                    <div class='range-slider'>
                                        <input type="range" min="-30" max="30" step="1" v-model="filters.spread.low" @change="slider()">
                                        <input type="range" min="-30" max="30" step="1" v-model="filters.spread.high" @change="slider()">
                                    </div>
                                    <div class="select-container">
                                        <!-- Customizable filter options will be populated here -->
                                        <label for="spread-low">Low</label><br>
                                        <input id="spread-low" type="text" v-model="filters.spread.low"/>
                                    </div>
                                    
                                    <div class="select-container">
                                        <label for="spread-high">High</label><br>
                                        <input id="spread-high" type="text" v-model="filters.spread.high"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="custom-dropdown" :class="{ 'dropdown-filter-applied': filters.week.low || filters.week.high }">
                            <div class="selected-value" @click="selectedFilter = (selectedFilter == 'week' ? null : 'week')">
                                <span v-if="!(filters.week.low || filters.week.high)">Weeks</span>
                                <span v-else>{{ filters.week.low ? "Week "+filters.week.low : "Before" }} {{ (filters.week.high && filters.week.low) ? "to" : "" }} {{ filters.week.high ? "Week "+filters.week.high : "and after" }}</span>
                                <i v-show="selectedFilter != 'week'" class="fa-solid fa-caret-right align-right"></i>
                                <i v-show="selectedFilter == 'week'" class="fa-solid fa-caret-down align-right"></i>
                            </div>
                            <div class="dropdown-container" v-show="selectedFilter == 'week'">
                                <div class="dropdown-options">
                                    <div class="select-container">
                                        <!-- Customizable filter options will be populated here -->
                                        <label for="week-low">Low</label><br>
                                        <input id="week-low" type="text" v-model="filters.week.low"/>
                                    </div>
                                    
                                    <div class="select-container">
                                        <label for="week-high">High</label><br>
                                        <input id="week-high" type="text" v-model="filters.week.high"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="custom-dropdown" :class="{ 'dropdown-filter-applied': filters.total.low || filters.total.high }">
                            <div class="selected-value" @click="selectedFilter = (selectedFilter == 'total' ? null : 'total')">
                                <span v-if="!(filters.total.low || filters.total.high)">Total</span>
                                <span v-else>{{ filters.total.low ? filters.total.low : "less than" }} {{ (filters.total.high && filters.total.low) ? "to" : "" }} {{ filters.total.high ? filters.total.high : "or more" }}</span>
                                <i v-show="selectedFilter != 'total'" class="fa-solid fa-caret-right align-right"></i>
                                <i v-show="selectedFilter == 'total'" class="fa-solid fa-caret-down align-right"></i>
                            </div>
                            <div class="dropdown-container" v-show="selectedFilter == 'total'">
                                <div class="dropdown-options">
                                    <div class="select-container">
                                        <!-- Customizable filter options will be populated here -->
                                        <label for="total-low">Low</label><br>
                                        <input id="total-low" type="text" v-model="filters.total.low"/>
                                    </div>
                                    
                                    <div class="select-container">
                                        <label for="total-high">High</label><br>
                                        <input id="total-high" type="text" v-model="filters.total.high"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="custom-dropdown" :class="{ 'dropdown-filter-applied': filters.divisional}">
                            <div class="selected-value" @click="selectedFilter = (selectedFilter == 'divisional' ? null : 'divisional')">
                                <span v-if="!filters.divisional">Divisional Filter</span>
                                <span v-else-if="filters.divisional == 'divisional'">Division Only</span>
                                <span v-else-if="filters.divisional == 'nondivisional'">Nondivision Only</span>
                                <i v-show="selectedFilter != 'divisional'" class="fa-solid fa-caret-right align-right"></i>
                                <i v-show="selectedFilter == 'divisional'" class="fa-solid fa-caret-down align-right"></i>
                            </div>
                            <div class="dropdown-container" v-show="selectedFilter == 'divisional'">
                                <div class="dropdown-options">
                                    <select class="default-select" v-model="filters.divisional">
                                        <option :value="null">N/A</option>
                                        <option value="divisional">Divisional</option>
                                        <option value="nondivisional">Non-divisional</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="custom-dropdown" :class="{ 'dropdown-filter-applied': filters.homeaway}">
                            <div class="selected-value" @click="selectedFilter = (selectedFilter == 'homeaway' ? null : 'homeaway')">
                                <span v-if="!filters.homeaway">Home/Away Filter</span>
                                <span v-else-if="filters.homeaway == 'home'">Home Only</span>
                                <span v-else-if="filters.homeaway == 'away'">Away Only</span>
                                <span v-else-if="filters.homeaway == 'neutral'">Neutral Only</span>
                                <i v-show="selectedFilter != 'homeaway'" class="fa-solid fa-caret-right align-right"></i>
                                <i v-show="selectedFilter == 'homeaway'" class="fa-solid fa-caret-down align-right"></i>
                            </div>
                            <div class="dropdown-container" v-show="selectedFilter == 'homeaway'">
                                <div class="dropdown-options">
                                    <select class="default-select" v-model="filters.homeaway">
                                        <option :value="null">N/A</option>
                                        <option value="home">Home</option>
                                        <option value="away">Away</option>
                                        <option value="neutral">Neutral</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="custom-dropdown" :class="{ 'dropdown-filter-applied': filters.gametype}">
                            <div class="selected-value" @click="selectedFilter = (selectedFilter == 'gametype' ? null : 'gametype')">
                                <span v-if="!filters.gametype">Game Type</span>
                                <span v-else-if="filters.gametype == 'regular'">Regular Season</span>
                                <span v-else-if="filters.gametype == 'playoffs'">Playoffs</span>
                                <i v-show="selectedFilter != 'gametype'" class="fa-solid fa-caret-right align-right"></i>
                                <i v-show="selectedFilter == 'gametype'" class="fa-solid fa-caret-down align-right"></i>
                            </div>
                            <div class="dropdown-container" v-show="selectedFilter == 'gametype'">
                                <div class="dropdown-options">
                                    <select class="default-select" v-model="filters.gametype">
                                        <option :value="null">N/A</option>
                                        <option value="regular">Regular Season</option>
                                        <option value="playoffs">Playoffs</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="custom-dropdown" :class="{ 'dropdown-filter-applied': filters.daysrest.low || filters.daysrest.high }">
                            <div class="selected-value" @click="selectedFilter = (selectedFilter == 'daysrest' ? null : 'daysrest')">
                                <span v-if="!(filters.daysrest.low || filters.daysrest.high)">Team Rest</span>
                                <span v-else>{{ filters.daysrest.low ? "Rested "+filters.daysrest.low : "Less than" }} {{ (filters.daysrest.high && filters.daysrest.low) ? "to" : "" }} {{ filters.daysrest.high ? filters.daysrest.high+" Days" : "or more"}}</span>
                                <i v-show="selectedFilter != 'daysrest'" class="fa-solid fa-caret-right align-right"></i>
                                <i v-show="selectedFilter == 'daysrest'" class="fa-solid fa-caret-down align-right"></i>
                            </div>
                            <div class="dropdown-container" v-show="selectedFilter == 'daysrest'">
                                <div class="dropdown-options">
                                    <div class="select-container">
                                        <!-- Customizable filter options will be populated here -->
                                        <label for="daysrest-low">Low</label><br>
                                        <input id="daysrest-low" type="text" v-model="filters.daysrest.low"/>
                                    </div>
                                    
                                    <div class="select-container">
                                        <label for="daysrest-high">High</label><br>
                                        <input id="daysrest-high" type="text" v-model="filters.daysrest.high"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="custom-dropdown" :class="{ 'dropdown-filter-applied': filters.opprest.low || filters.opprest.high }">
                            <div class="selected-value" @click="selectedFilter = (selectedFilter == 'opprest' ? null : 'opprest')">
                                <span v-if="!(filters.opprest.low || filters.opprest.high)">Opp Rest</span>
                                <span v-else>Opp {{ filters.opprest.low ? "Rested "+filters.opprest.low : "Less than" }} {{ (filters.opprest.high && filters.opprest.low) ? "to" : "" }} {{ filters.opprest.high ? filters.opprest.high+" Days" : "or more"}}</span>
                                <i v-show="selectedFilter != 'opprest'" class="fa-solid fa-caret-right align-right"></i>
                                <i v-show="selectedFilter == 'opprest'" class="fa-solid fa-caret-down align-right"></i>
                            </div>
                            <div class="dropdown-container" v-show="selectedFilter == 'opprest'">
                                <div class="dropdown-options">
                                    <div class="select-container">
                                        <!-- Customizable filter options will be populated here -->
                                        <label for="opprest-low">Low</label><br>
                                        <input id="opprest-low" type="text" v-model="filters.opprest.low"/>
                                    </div>
                                    
                                    <div class="select-container">
                                        <label for="opprest-high">High</label><br>
                                        <input id="opprest-high" type="text" v-model="filters.opprest.high"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="custom-dropdown" :class="{ 'dropdown-filter-applied': filters.lastresult}">
                            <div class="selected-value" @click="selectedFilter = (selectedFilter == 'lastresult' ? null : 'lastresult')">
                                <span v-if="filters.lastresult === null">Last Result</span>
                                <span v-else-if="filters.lastresult == 'covered'">Covered Last Game</span>
                                <span v-else-if="filters.lastresult == 'no_cover'">No Cover Last Game</span>
                                <i v-show="selectedFilter != 'lastresult'" class="fa-solid fa-caret-right align-right"></i>
                                <i v-show="selectedFilter == 'lastresult'" class="fa-solid fa-caret-down align-right"></i>
                            </div>
                            <div class="dropdown-container" v-show="selectedFilter == 'lastresult'">
                                <div class="dropdown-options">
                                    <select class="default-select" v-model="filters.lastresult">
                                        <option :value="null">N/A</option>
                                        <option value="covered">Covered</option>
                                        <option value="no_cover">Did Not Cover</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="custom-dropdown" :class="{ 'dropdown-filter-applied': filters.lastlocation}">
                            <div class="selected-value" @click="selectedFilter = (selectedFilter == 'lastlocation' ? null : 'lastlocation')">
                                <span v-if="!filters.lastlocation">Last Location</span>
                                <span v-else-if="filters.lastlocation == 'home'">Last Game Home</span>
                                <span v-else-if="filters.lastlocation == 'away'">Last Game Away</span>
                                <i v-show="selectedFilter != 'lastlocation'" class="fa-solid fa-caret-right align-right"></i>
                                <i v-show="selectedFilter == 'lastlocation'" class="fa-solid fa-caret-down align-right"></i>
                            </div>
                            <div class="dropdown-container" v-show="selectedFilter == 'lastlocation'">
                                <div class="dropdown-options">
                                    <select class="default-select" v-model="filters.lastlocation">
                                        <option :value="null">N/A</option>
                                        <option value="home">Home</option>
                                        <option value="away">Away</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="record-container">
                            <button v-on:click="fetchSchedule()" style="margin-top: 10px;">CLICK HERE TO UPDATE</button>
                            <div v-if="record.ATS" style="margin-top: 10px">
                                ATS: {{ record.ATS.Win }} - {{ record.ATS.Loss }} ({{ record.ATS.Units }}u, {{ record.ATS.ROI }}% ROI)
                                <br/>
                                SU: {{ record.SU.Win }} - {{ record.SU.Loss }} ({{ record.SU.Units }}u, {{ record.SU.ROI }}% ROI)
                            </div>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
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
</template>

<script>
export default {
    name: 'SystemBuilder',
    // other component properties...
    data() {
        return {
            schedule: [],
            record: [],
            selectedFilter: null,
            filters: {
                spread: {
                    low: -30,
                    high: 30
                },
                week: {
                    low: null,
                    high: null
                },
                total: {
                    low: null,
                    high: null
                },
                daysrest: {
                    low: null,
                    high: null
                },
                opprest: {
                    low: null,
                    high: null
                },
                divisional: null,
                homeaway: null,
                gametype: null,
                lastresult: null,
                lastlocation: null,
            }
        };
    },
    mounted() {
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
        slider: function() {
            if (this.filters.spread.low > this.filters.spread.high) {
                var tmp = this.filters.spread.high;
                this.filters.spread.high = this.filters.spread.low;
                this.filters.spread.low = tmp;
            }
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
        overflow-y: auto;
        height: 100vh;
    }

    .styled-table {
        border-collapse: collapse;
        border-spacing: 0;
        table-layout: auto; /* Set table layout to auto */
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
        position: sticky;
        top: 0;
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

    /* Additional styles for the custom dropdown */
    .custom-dropdown {
        position: relative;
        display: inline-block;
        width: 200px;
        background-color: #FFFFFF;
        margin-right: 5px;
        margin-top: 5px;
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

    .dropdown-container {
        position: absolute;
        z-index: 1;
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        width: 200px;
    }

    /* Show options when the dropdown is expanded */
    .custom-dropdown.expanded .dropdown-options {
        display: block;
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
    
</style>