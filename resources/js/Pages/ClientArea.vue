<script>
import API from "../apiConig.json";
import InfiniteLoading from "v3-infinite-loading";
import "v3-infinite-loading/lib/style.css";
import TextAreaInput from "@/Components/TextAreaInput.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { notify } from "@kyvg/vue3-notification";

export default {
    components: {
        "infinite-loading": InfiniteLoading,
        TextAreaInput,
        InputError,
        InputLabel,
        PrimaryButton,
        TextInput,
        TextAreaInput
    },
    data() {
        return {
            // Main Api URL
            api: API.API_URL,
            page: 1,
            tickets: [],
            toggleForm: false,
            errorMsg: "",
            searching: '',
            isSearching: false,
            formSubmit: false,
            detail: null,
            selectedTicket: {
                id: null,
                date: null,
                ticketId: null,
                customer: null,
                email: null,
                contact: null,
                problem: null,
                reply: null,
                replyedBy: null,
                replyDate: null
            }

        }
    },
    async mounted() {
    },

    methods: {
        async getCustomerRevies() {
            const vm = this;
            console.log('call ', vm.page);
            await axios.get(vm.api + "/tickets?page=" + vm.page).then((response) => {
                // vm.tickets = response.data.data;
                vm.tickets.push(...response.data.data);
                vm.page++;
            }).catch((e) => {
                console.log('Unauthenticated');
            });

        },

        async searchTicket() {
            const vm = this;

            if (vm.searching.length > 4) {
                vm.isSearching = true;
                await axios.get(vm.api + "/tickets/" + vm.searching).then((response) => {
                    vm.tickets = response.data.data;
                }).catch((e) => {
                    notify({
                        title: "Authorization",
                        text: "Please try again",
                        type: 'error'
                    });
                });
            }
            if (vm.searching == '') {
                vm.page = 1;
                vm.tickets = [];
                vm.isSearching = false;
                vm.getCustomerRevies();
            }
        },


        selectNow(selected) {
            const vm = this;
            vm.selectedTicket = {
                id: selected.ticketObjId,
                date: selected.created,
                ticketId: selected.ticketId,
                customer: selected.customerName,
                email: selected.email,
                contact: selected.contact,
                problem: selected.detail,
                reply: selected.reply,
                replyedBy: selected.repliedBy,
                replyDate: selected.repliedTime
            };
            vm.toggleForm = true;

        },

        createNewTicket() {
            const vm = this;
            if (vm.detail == null) {
                vm.errorMsg = "please add your problem";
            } else {
                vm.formSubmit = true;
                axios({
                    method: "POST",
                    url: vm.api + "/tickets",
                    data: { 'detail': vm.detail }
                }).then(function (response) {
                    if (response.data.status) {
                        vm.page = 1;
                        vm.tickets = [];
                        vm.getCustomerRevies();
                        $('#exampleModal').modal('hide');
                        notify({
                            title: "Success",
                            text: response.data.message,
                            type: 'success'
                        });
                        vm.formSubmit = false;
                    }
                }).catch((e) => {
                    vm.formSubmit = false;
                    // console.log(e);
                    // console.log('Unauthenticated');
                    notify({
                        title: "Authorization",
                        text: 'Please try again later',
                        type: 'error'
                    });
                });
            }

        },

        resetForm() {
            const vm = this;
            vm.toggleForm = false;
            vm.selectedTicket = {
                id: null,
                date: null,
                ticketId: null,
                customer: null,
                email: null,
                contact: null,
                problem: null,
                reply: null,
                replyedBy: null,
                replyDate: null
            };
        },

    }
}
</script>
<template>
    <div class="row mt-4">
        <div class="col-lg-7 col-sm-11">
            <InputLabel for="search" :value="isSearching ? 'Wait...' : 'Search...'" />
            <TextInput id="search" v-model="searching" @input="searchTicket()" type="text" class="mt-1 block w-full" />
        </div>
        <div class="col-lg-1 col-sm-1">
            <button class="btn btn-primary mt-4" data-bs-toggle="modal" data-bs-target="#exampleModal">
                New
            </button>
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Open New Ticket</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <InputLabel for="detail" value="Problem Description" />
                            <TextAreaInput id="detail" class="mt-1 block w-full" v-model="detail" />
                            <InputError class="mt-2" :message="errorMsg" />
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" @click="detail = null;" data-bs-dismiss="modal"
                                :disabled="formSubmit">Close</button>
                            <button class="btn btn-primary" @click="createNewTicket()" :disabled="formSubmit">
                                {{ formSubmit ? 'Wait...' : 'Create' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-lg-8 col-sm-6">
            <div v-if="tickets.length == 0">
                <div class="card-body">
                    <div class="row pr-4">
                        <div class="col-lg-12">
                            <span class="span">No Ticket Found</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="max-w-7xl mx-auto sm:px-4 lg:px-4 space-y-6">
                <div class="box p-4 sm:p-8 bg-white shadow sm:rounded-lg" v-for="ticket in tickets" :key="ticket.id">
                    <div v-if="ticket.reply == null" class="ribbon-2">Pending</div> 
                    <div class="row pr-4">
                        <div class="col-lg-11">
                            <h5 class="card-title" :class="ticket.isOpen ? '' : 'textBold'"># {{
                                    ticket.ticketId
                            }}
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <span class="span">{{ ticket.created }}</span>
                            </h5>

                            <p class="card-text">{{ ticket.detail }}</p>

                        </div>
                        <div class="col-lg-1 pt-4">
                            <a v-if="ticket.ticketObjId != selectedTicket.id" href="javascript:void(0);"
                                @click="selectNow(ticket)" class="btn btn-primary">
                                View
                            </a>
                            <a v-else href="javascript:void(0);" @click="resetForm()" class="btn btn-secondary">
                                Close
                            </a>
                        </div>
                    </div>
                </div>
                <infinite-loading spinner="spiral" @infinite="getCustomerRevies" />
            </div>
        </div>

        <div v-show="toggleForm" class="col-lg-4 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <h4># {{ selectedTicket.ticketId }}</h4>

                    <div class="row mt-3">
                        <div class="col-12">
                            <InputLabel value="Problem" />
                            <InputLabel :value="selectedTicket.problem" />

                        </div>
                    </div>
                    <div v-if="selectedTicket.reply != null">
                        <div class="row mt-3">
                            <div class="col-4">
                                <InputLabel value="Replyed By" />
                            </div>
                            <div class="col-8">
                                <InputLabel :value="selectedTicket.replyedBy" />
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-4">
                                <InputLabel value="Reply Date" />
                            </div>
                            <div class="col-8">
                                <InputLabel :value="selectedTicket.replyDate" />
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <InputLabel value="Reply" />
                                <InputLabel :value="selectedTicket.reply" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>