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
        TextInput
    },
    data() {
        return {
            // Main Api URL
            api: API.API_URL,
            page: 1,
            tickets: [],
            comments: [],
            toggleForm: false,
            errorMsg: "",
            searching: '',
            isSearching: false,
            replyBtnDisable: false,
            responseForm: {
                reply: null,
                type: 'reply'
            },
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
        // this.getCustomerRevies();
        // this.getTicketById(2, '6OXTQet1KWoj');
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
                    if (response.data.status) {
                        vm.tickets = response.data.data;
                    } else {
                        notify({
                            title: "Failed",
                            text: response.data.message,
                            type: 'error'
                        });
                    }
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

        async markAsOpen() {
            const vm = this;
            const params = new URLSearchParams();
            params.append("type", 'markOpen');
            axios({
                method: "PUT",
                url: vm.api + "/tickets/" + vm.selectedTicket.id,
                data: params
            }).then(function (response) {
                if (response.data.status) {
                    vm.tickets.find(ticket => {
                        if (ticket.ticketObjId === vm.selectedTicket.id) {
                            ticket.isOpen = true;
                        }
                    });
                }
            }).catch((e) => {
                console.log('Unauthenticated');
            });
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
            if (!selected.isOpen) {
                vm.markAsOpen();
            }
        },

        async submitReply() {
            const vm = this;
            if (vm.responseForm.reply == null) {
                vm.errorMsg = "please add your reply";
            } else {
                vm.replyBtnDisable = true;
                axios({
                    method: "PUT",
                    url: vm.api + "/tickets/" + vm.selectedTicket.id,
                    data: vm.responseForm
                }).then(function (response) {
                    vm.replyBtnDisable = false;
                    if (response.data.status) {
                        vm.getTicketById(vm.selectedTicket.id, vm.selectedTicket.ticketId);
                        notify({
                            title: "Success",
                            text: response.data.message,
                            type: 'success'
                        });
                        vm.resetForm();
                    } else {
                        notify({
                            title: "Failed",
                            text: response.data.message,
                            type: 'error'
                        });
                    }
                }).catch((e) => {
                    vm.replyBtnDisable = false;
                    console.log('Unauthenticated');
                });
            }
        },

        async getTicketById(objectId, ticketId) {
            const vm = this;
            await axios.get(vm.api + "/tickets/" + objectId, {
                params: {
                    'type': 'replace', 'ticketId': ticketId
                }
            }).then((response) => {
                if (response.data.status) {
                    var thisTicket = response.data.data[0];
                    if (thisTicket.length > 0) {
                        vm.tickets.find((ticket, index) => {
                            console.log(ticket);
                            if (ticket.ticketObjId === objectId) {
                                vm.tickets.splice(index, 1);
                                vm.tickets[index] = thisTicket;
                            }
                        });
                    }

                }
            }).catch((e) => {
                console.log(e);
                // notify({
                //     title: "Authorization",
                //     text: "Please try again",
                //     type: 'error'
                // });
            });
        },

        resetForm() {
            const vm = this;
            vm.toggleForm = false;
            vm.errorMsg = "";

            vm.responseForm = {
                reply: null,
                type: 'reply'
            };
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
        }

    }
}
</script>
<template>
    <div class="row mt-4">
        <div class="col-lg-8 col-sm-12">
            <InputLabel for="search" :value="isSearching ? 'Wait...' : 'Search...'" />
            <TextInput id="search" v-model="searching" @input="searchTicket()" type="text" class="mt-1 block w-full" />
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

            <div class="scrollDiv">
                <div class="box p-4 sm:p-8 bg-white shadow sm:rounded-lg" v-for="ticket in tickets" :key="ticket.id">
                    <div v-if="!ticket.isOpen || ticket.reply == null" class="ribbon-2">Pending</div>
                    <div class="row pr-4">
                        <div class="col-lg-11">

                            <h5 class="card-title" :class="ticket.isOpen ? '' : 'textBold'"># {{
                                    ticket.ticketId
                            }}
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <span class="span">{{ ticket.created }}</span>
                            </h5>
                            <p class="card-text" :class="ticket.isOpen ? '' : 'textBold'">
                                Customer : <span class="span">{{ ticket.customerName }}</span>
                                &nbsp;&nbsp;
                                Email : <span class="span"><a :href="'mailto:' + ticket.email"></a>{{
                                        ticket.email
                                }}</span>
                                &nbsp;&nbsp;
                                Contact : <span class="span"><a :href="'tel:' + ticket.contact"></a>{{
                                        ticket.contact
                                }}</span>
                            </p>
                            <p class="card-text">{{ ticket.detail }}</p>

                        </div>
                        <div class="col-lg-1 pt-4">
                            <a v-if="ticket.ticketObjId != selectedTicket.id" href="javascript:void(0);"
                                @click="selectNow(ticket)" class="btn btn-primary">
                                {{ ticket.isOpen && ticket.reply != null ? 'View' : 'Reply' }}
                            </a>
                            <a v-else href="javascript:void(0);" @click="resetForm()" class="btn btn-secondary">
                                Close
                            </a>
                        </div>
                    </div>
                </div>
                <infinite-loading @infinite="getCustomerRevies" />
            </div>
        </div>

        <div v-show="toggleForm" class="col-lg-4 col-sm-6 scrollDiv2 mt-3">
            <div class="card">
                <div class="card-body">
                    <h4># {{ selectedTicket.ticketId }}</h4>
                    <div class="row mt-3">
                        <div class="col-4">
                            <InputLabel value="Customer" />
                        </div>
                        <div class="col-8">
                            <InputLabel :value="selectedTicket.customer" />
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-4">
                            <InputLabel value="Email" />
                        </div>
                        <div class="col-8">
                            <InputLabel :value="selectedTicket.email" />
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-4">
                            <InputLabel value="Contact" />
                        </div>
                        <div class="col-8">
                            <InputLabel :value="selectedTicket.contact" />
                        </div>
                    </div>
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
                    <div v-else>
                        <div class="row mt-3">
                            <form @submit.prevent="submitReply()">
                                <InputLabel for="detail" value="Reply" />
                                <TextAreaInput id="detail" class="mt-1 block w-full" v-model="responseForm.reply"
                                    required />
                                <InputError class="mt-2" :message="errorMsg" />
                                <PrimaryButton class="mt-3 ml-4" :disabled="replyBtnDisable">
                                    Reply
                                </PrimaryButton>
                            </form>
                        </div>
                    </div>




                </div>
            </div>
        </div>
    </div>


</template>