<script>
import API from "../apiConig.json";
import InfiniteLoading from "v3-infinite-loading";
import "v3-infinite-loading/lib/style.css";

export default {
    components: {
        "infinite-loading": InfiniteLoading,
    },
    data() {
        return {
            // Main Api URL
            api: API.API_URL,
            page: 1,
            tickets: [],
            comments: [],
            searching: 'a.triple93@yahoo.com',
            responseForm: {
                reply: null
            },

        }
    },
    async mounted() {
        this.getCustomerRevies();
        this.searchTicket();
    },
    methods: {
        async getCustomerRevies() {
            const vm = this;
            await axios.get(vm.api + "/tickets?page=" + vm.page).then((response) => {
                vm.tickets = response.data.data;
            }).catch((e) => {
                console.log('Unauthenticated');
            });

        },

        async searchTicket() {
            const vm = this;
            await axios.get(vm.api + "/tickets/" + vm.searching).then((response) => {
                vm.tickets = response.data.data;
            }).catch((e) => {
                console.log('Unauthenticated');
            });
        },

        async markAsOpen() {
            const vm = this;
            axios({
                method: "PUT",
                url: vm.api + "/tickets/" + 1,
                data: vm.createAircraftForm,
            }).then(function (response) {

            }).catch((e) => {
                console.log('Unauthenticated');
            });
        },

        async loadData(state) {
            console.log("loading...", state);
            try {
                const response = await fetch(
                    "https://jsonplaceholder.typicode.com/comments?_limit=10&_page=" +
                    this.page
                );
                const json = await response.json();
                if (json.length < 10) state.complete();
                else {
                    this.comments.push(...json);
                    state.loaded();
                }
                this.page++;
            } catch (error) {
                state.error();
            }
        },


    }
}
</script>
<template>
    <!-- <table>
        <tr>
            <th>Ticket No.</th>
            <th>Customer</th>
            <th>Email</th>
            <th>Contact</th>
            <th>Problem</th>
            <th>Responsed</th>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </table> -->
    <div class="mt-4">
        <div class="card" v-for="comment in comments" :key="comment.id">
        <div class="card-body">
            <h5 class="card-title">{{ comment.email }}</h5>
            <p class="card-text">{{ comment.id }}</p>
            <a href="#" class="btn btn-primary">Go somewhere</a>
        </div>
    </div>
    <infinite-loading @infinite="loadData" />
    
    </div>
    
</template>