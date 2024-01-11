<template>
    <div class="custom-dropdown" :class="{ 'dropdown-filter-applied': isFilterActive }">
        <div class="selected-value" @click="selectedFilter = (selectedFilter == filterName ? null : filterName)">
            <span v-if="!isFilterActive">{{ filterName }}</span>
            <span v-else>{{ filterName }}: {{ filter.low }} to {{ filter.high }}</span>
            <i v-show="selectedFilter != filterName" class="fa-solid fa-caret-right align-right"></i>
            <i v-show="selectedFilter == filterName" class="fa-solid fa-caret-down align-right"></i>
        </div>
        <div class="dropdown-container" v-show="selectedFilter == filterName">
            <div class="dropdown-options">
                <div class='range-slider'>
                    <input type="range" :min="config.min" :max="config.max" step="1" v-model="filter.low">
                    <input type="range" :min="config.min" :max="config.max" step="1" v-model="filter.high">
                </div>
                <div class="select-container">
                    <label :for="filterName+'-low'">Low</label><br>
                    <input :id="filterName+'-low'" type="text" v-model="filter.low"/>
                </div>
                
                <div class="select-container">
                    <label :for="filterName+'-high'">High</label><br>
                    <input :id="filterName+'-high'" type="text" v-model="filter.high"/>
                </div>
            </div>
        </div>
    </div>  
</template>

<script>
export default {
    name: 'HighLowDropdown',
    props: {
        filter: {
            low: Number,
            high: Number
        },
        config: {},
        selectedFilter: null,
        filterName: String
    },
    // other component properties...
    data() {
        return {
            filterChangeTimeout: null
        };
    },
    watch: {
        'filter.low'(newVal) {
            this.handleFilterChange();
        },
        'filter.high'(newVal) {
            this.handleFilterChange();
        },
    },
    computed: {
        isHighFilterActive() {
            return this.filter.high < this.config.max;
        },
        isLowFilterActive() {
            return this.filter.low > this.config.min;
        },
        isFilterActive() {
            return this.isHighFilterActive || this.isLowFilterActive;
        }
    },
    mounted() {
    },
    methods: {
        handleFilterChange(){
            clearTimeout(this.filterChangeTimeout);
            this.filterChangeTimeout = setTimeout(() => {
                this.filter.low = this.filter.low < this.config.min ? this.config.min : this.filter.low;
                this.filter.high = this.filter.high > this.config.max ? this.config.max : this.filter.high;
                if (parseInt(this.filter.high) < parseInt(this.filter.low)) { 
                    let tmp = this.filter.high;
                    this.filter.high = this.filter.low;
                    this.filter.low = tmp;
                }
                this.$emit('filter-changed',this.filter, this.filterName);
            }, 1000);
        }
    },
}
</script>

<style>
    
</style>