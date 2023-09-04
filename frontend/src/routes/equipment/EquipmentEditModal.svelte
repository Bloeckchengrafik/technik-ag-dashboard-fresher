<script lang="ts">
    import type {EquipmentSpec, UserSpec} from "../../api";
    import {apiGet, apiPost} from "../../api";
    import Modal from "../../lib/Modal.svelte";
    import {createEventDispatcher} from "svelte";
    import TextInput from "../../lib/Forms/TextInput.svelte";
    import Swal from "sweetalert2";
    import SubmitBtn from "../../lib/Forms/SubmitBtn.svelte";

    export let equipment: EquipmentSpec | null = null;
    export let user: UserSpec
    let dispatch = createEventDispatcher();

    let count = "1";
    let name = "";
    let category_id = 0;
    let location_id = 0;
    let manufacturer_id = 0;

    let queryNewPromise: Promise<{
        categories: {
            id: number;
            name: string;
        }[];
        locations: {
            id: number;
            name: string;
        }[];
        manufacturers: {
            id: number;
            name: string;
        }[];
    }> = new Promise(() => {
    });

    async function reAskQueryNew() {
        queryNewPromise = Promise.resolve(await apiGet("v1/equipment/queryNew.php").then(r => r.json()));
    }

    function prepare() {
        reAskQueryNew();

        if (equipment) {
            count = "" + equipment.count;
            name = equipment.name;
            category_id = equipment.category_id;
            location_id = equipment.location_id;
            manufacturer_id = equipment.manufacturer_id;
        } else {
            count = "1";
            name = "";
            category_id = 0;
            location_id = 0;
            manufacturer_id = 0;
        }
    }

    let initialized = false;
    if (!initialized) {
        initialized = true;
        prepare();
    }

    let newCategory = "";
    let newLocation = "";
    let newManufacturer = "";
</script>

<Modal on:close={() => {
        dispatch("update");
    }}>

    {#await queryNewPromise}
        <p>Loading...</p>
    {:then queryData}        <h1 class="text-2xl font-bold mb-4">
        {equipment ? "Gerät bearbeiten" : "Neues Gerät anlegen"}
    </h1>
        <div class="flex flex-row gap-4">
            <TextInput id="name" name="Name" bind:value={name}/>
            <TextInput id="count" name="Anzahl" type="number" bind:value={count}/>
        </div>

        <h2 class="text-xl font-bold mt-4 mb-2">Kategorie</h2>
        <div class="flex flex-col gap-2">
            {#each queryData.categories as category}
                <label class="flex flex-row justify-between items-center gap-2">
                    <div>
                        <input type="radio" name="category_id" value={category.id}
                               class="mr-2"
                               checked={category.id === category_id}
                               required
                               on:change={() => category_id = category.id}
                        />
                        <span class="text-md">{category.name}</span>
                    </div>
                    {#if user.permission.includes("equipmentChange")}
                        <button class="rounded bg-red-500 hover:bg-red-400 text-white px-2 py-1"
                                on:click={async () => {
                                    Swal.fire({
                                        title: 'Kategorie löschen?',
                                        text: "Die Kategorie und alle Geräte in dieser Kategorie werden unwiderruflich gelöscht!",
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#d33',
                                        cancelButtonColor: '#3085d6',
                                        confirmButtonText: 'Löschen',
                                        cancelButtonText: 'Abbrechen'
                                    }).then(async (result) => {
                                        if (result.isConfirmed) {
                                            await apiGet("v1/equipment/delete_category.php?id=" + category.id);
                                            await reAskQueryNew();
                                        }
                                    })
                                }}
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                 viewBox="0 0 256 256">
                                <path d="M216,48H176V40a24,24,0,0,0-24-24H104A24,24,0,0,0,80,40v8H40a8,8,0,0,0,0,16h8V208a16,16,0,0,0,16,16H192a16,16,0,0,0,16-16V64h8a8,8,0,0,0,0-16ZM96,40a8,8,0,0,1,8-8h48a8,8,0,0,1,8,8v8H96Zm96,168H64V64H192ZM112,104v64a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Zm48,0v64a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Z"></path>
                            </svg>
                        </button>
                    {/if}
                </label>
            {/each}
            {#if user.permission.includes("equipmentChange")}
                <!-- Add new -->
                <div class="flex flex-row gap-2 items-center">
                    <TextInput id="new_category" name="Neue Kategorie" bind:value={newCategory}/>
                    <button class="rounded bg-green-500 hover:bg-green-400 text-white px-2 py-1 h-fit"
                            on:click={async () => {
                                await apiPost("v1/equipment/new_category.php", {
                                    name: newCategory
                                });
                                newCategory = "";
                                await reAskQueryNew();
                            }}
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                             viewBox="0 0 256 256">
                            <path d="M224,128a8,8,0,0,1-8,8H136v80a8,8,0,0,1-16,0V136H40a8,8,0,0,1,0-16h80V40a8,8,0,0,1,16,0v80h80A8,8,0,0,1,224,128Z"></path>
                        </svg>
                    </button>
                </div>
            {/if}
        </div>

        <h2 class="text-xl font-bold mt-4 mb-2">Standort</h2>
        <div class="flex flex-col gap-2">
            {#each queryData.locations as location}
                <label class="flex flex-row justify-between items-center gap-2">
                    <div>
                        <input type="radio" name="loc_id" value={location.id}
                               class="mr-2"
                               checked={location.id === location_id}
                               required
                               on:change={() => location_id = location.id}
                        />
                        <span class="text-md">{location.name}</span>
                    </div>
                    {#if user.permission.includes("equipmentChange")}
                        <button class="rounded bg-red-500 hover:bg-red-400 text-white px-2 py-1"
                                on:click={async () => {
                                    Swal.fire({
                                        title: 'Ort löschen?',
                                        text: "Dieser Lagerort und alle Geräte in diesem Lager werden unwiderruflich gelöscht!",
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#d33',
                                        cancelButtonColor: '#3085d6',
                                        confirmButtonText: 'Löschen',
                                        cancelButtonText: 'Abbrechen'
                                    }).then(async (result) => {
                                        if (result.isConfirmed) {
                                            await apiGet("v1/equipment/delete_location.php?id=" + location.id);
                                            await reAskQueryNew();
                                        }
                                    })
                                }}
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                 viewBox="0 0 256 256">
                                <path d="M216,48H176V40a24,24,0,0,0-24-24H104A24,24,0,0,0,80,40v8H40a8,8,0,0,0,0,16h8V208a16,16,0,0,0,16,16H192a16,16,0,0,0,16-16V64h8a8,8,0,0,0,0-16ZM96,40a8,8,0,0,1,8-8h48a8,8,0,0,1,8,8v8H96Zm96,168H64V64H192ZM112,104v64a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Zm48,0v64a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Z"></path>
                            </svg>
                        </button>
                    {/if}
                </label>
            {/each}
            {#if user.permission.includes("equipmentChange")}
                <!-- Add new -->
                <div class="flex flex-row gap-2 items-center">
                    <TextInput id="new_category" name="Neues Lager" bind:value={newLocation}/>
                    <button class="rounded bg-green-500 hover:bg-green-400 text-white px-2 py-1 h-fit"
                            on:click={async () => {
                                await apiPost("v1/equipment/new_location.php", {
                                    name: newLocation
                                });
                                newLocation = "";
                                await reAskQueryNew();
                            }}
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                             viewBox="0 0 256 256">
                            <path d="M224,128a8,8,0,0,1-8,8H136v80a8,8,0,0,1-16,0V136H40a8,8,0,0,1,0-16h80V40a8,8,0,0,1,16,0v80h80A8,8,0,0,1,224,128Z"></path>
                        </svg>
                    </button>
                </div>
            {/if}
        </div>

        <h2 class="text-xl font-bold mt-4 mb-2">Hersteller</h2>
        <div class="flex flex-col gap-2">
            {#each queryData.manufacturers as manufacturer}
                <label class="flex flex-row justify-between items-center gap-2">
                    <div>
                        <input type="radio" name="manuf_id" value={manufacturer.id}
                               class="mr-2"
                               checked={manufacturer.id === manufacturer_id}
                               required
                               on:change={() => manufacturer_id = manufacturer.id}
                        />
                        <span class="text-md">{manufacturer.name}</span>
                    </div>
                    {#if user.permission.includes("equipmentChange")}
                        <button class="rounded bg-red-500 hover:bg-red-400 text-white px-2 py-1"
                                on:click={async () => {
                                    Swal.fire({
                                        title: 'Hersteller löschen?',
                                        text: "Dieser Hersteller und alle Geräte von diesem Hersteller werden unwiderruflich gelöscht!",
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#d33',
                                        cancelButtonColor: '#3085d6',
                                        confirmButtonText: 'Löschen',
                                        cancelButtonText: 'Abbrechen'
                                    }).then(async (result) => {
                                        if (result.isConfirmed) {
                                            await apiGet("v1/equipment/delete_manufacturer.php?id=" + manufacturer.id);
                                            await reAskQueryNew();
                                        }
                                    })
                                }}
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                 viewBox="0 0 256 256">
                                <path d="M216,48H176V40a24,24,0,0,0-24-24H104A24,24,0,0,0,80,40v8H40a8,8,0,0,0,0,16h8V208a16,16,0,0,0,16,16H192a16,16,0,0,0,16-16V64h8a8,8,0,0,0,0-16ZM96,40a8,8,0,0,1,8-8h48a8,8,0,0,1,8,8v8H96Zm96,168H64V64H192ZM112,104v64a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Zm48,0v64a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Z"></path>
                            </svg>
                        </button>
                    {/if}
                </label>
            {/each}
            {#if user.permission.includes("equipmentChange")}
                <!-- Add new -->
                <div class="flex flex-row gap-2 items-center">
                    <TextInput id="new_category" name="Neuen Hersteller" bind:value={newManufacturer}/>
                    <button class="rounded bg-green-500 hover:bg-green-400 text-white px-2 py-1 h-fit"
                            on:click={async () => {
                                await apiPost("v1/equipment/new_manufacturer.php", {
                                    name: newManufacturer
                                });
                                newManufacturer = "";
                                await reAskQueryNew();
                            }}
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                             viewBox="0 0 256 256">
                            <path d="M224,128a8,8,0,0,1-8,8H136v80a8,8,0,0,1-16,0V136H40a8,8,0,0,1,0-16h80V40a8,8,0,0,1,16,0v80h80A8,8,0,0,1,224,128Z"></path>
                        </svg>
                    </button>
                </div>
            {/if}
        </div>

        <SubmitBtn on:click={async () => {
            if (equipment) {
                await apiPost("v1/equipment/update.php", {
                    id: equipment.id,
                    name,
                    count,
                    category_id,
                    location_id,
                    manufacturer_id
                });
            } else {
                await apiPost("v1/equipment/new.php", {
                    name,
                    count,
                    category_id,
                    location_id,
                    manufacturer_id
                });
            }
            dispatch("update");
            dispatch("closeme")
        }} name={equipment ? "Speichern" : "Anlegen"} />
    {/await}
</Modal>

