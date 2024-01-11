<template>
    <div class="custom-dropdown" :class="{ 'dropdown-filter-applied': curFilter }">
        <div class="selected-value" @click="selectedFilter = (selectedFilter == filterName ? null : filterName)">
            <span v-if="!curFilter">{{ filterName }}</span>
            <span v-else>{{ options[curFilter] }}</span>
            <i v-show="selectedFilter != filterName" class="fa-solid fa-caret-right align-right"></i>
            <i v-show="selectedFilter == filterName" class="fa-solid fa-caret-down align-right"></i>
        </div>
        <div class="dropdown-container" v-show="selectedFilter == filterName">
            <div class="dropdown-options">
                <div class="dropdown-options">
                    <select class="default-select" v-model="curFilter">
                        <option :value="null">N/A</option>
                        <option v-for="(item, index) in options" :key="index" :value="index">{{ item }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>  
</template>

<script>
export default {
    name: 'OptionDropdown',
    props: {
        filter: String|null,
        config: {},
        selectedFilter: null,
        filterName: String,
    },
    // other component properties...
    data() {
        return {
            options: this.config.options,
            curFilter: this.filter
        };
    },
    watch: {
        curFilter() {
            this.handleFilterChange();
        },
        filter() {
            this.curFilter = this.filter;
        },
    },
    computed: {
    },
    mounted() {
    },
    methods: {
        handleFilterChange(){
            this.$emit('filter-changed',this.curFilter, this.selectedFilter);
        }
    },
}
</script>

<style>

</style>