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
            curFilter: this.filter,
            options: this.config.options
        };
    },
    watch: {
        curFilter() {
            this.handleFilterChange();
        }
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
    .custom-dropdown {
        position: relative;
        display: inline-block;
        width: 100%;
        background-color: #FFFFFF;
        margin-right: 5px;
        margin-top: 5px;
    }

    /* Show options when the dropdown is expanded */
    .custom-dropdown.expanded .dropdown-options {
        display: block;
    }

    /* Additional styles for the custom dropdown */
    .dropdown-container {
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
</style>