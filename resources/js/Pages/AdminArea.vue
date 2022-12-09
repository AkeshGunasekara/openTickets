<script>
import API from "../apiConig.json";
import InfiniteLoading from "v3-infinite-loading";
import "v3-infinite-loading/lib/style.css";
import TextAreaInput from '@/Components/TextAreaInput.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';

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

        async submitReply(){
            const vm = this;
        }

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
    <div class="row mt-4">
        <div class="col-lg-8 col-sm-6">
            <div class="scrollDiv">
                <div class="card" v-for="comment in comments" :key="comment.id">
                    <div class="card-body">
                        <h5 class="card-title">{{ comment.email }}</h5>
                        <p class="card-text">{{ comment.id }}</p>
                        <a href="#" class="btn btn-primary">Go somewhere</a>
                    </div>
                </div>
            </div>
            <infinite-loading @infinite="loadData" />
        </div>
        <div class="col-lg-4 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <h4>#</h4>
                    <div class="row mt-3">
                        <div class="col-4">Customer</div>
                        <div class="col-8"></div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-4">Email</div>
                        <div class="col-8"></div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-4">Contact</div>
                        <div class="col-8"></div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-4">Problem</div>
                        <div class="col-8"></div>
                    </div>
                    
                        <div class="row mt-3">
                            <form @submit.prevent="submitReply()" >
                            <InputLabel for="detail" value="Reply" />
                            <TextAreaInput
                                id="detail"
                                class="mt-1 block w-full"
                                v-model="responseForm.reply"
                                required 
                            />
                            <InputError class="mt-2" :message="'asd'" />
                        </form>
                        </div>
                   
                    
                </div>
            </div>
        </div>
    </div>


</template>