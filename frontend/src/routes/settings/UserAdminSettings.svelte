<script lang="ts">
    import {apiGet, backend} from "../../api";
    import type {Permission} from "../../api";
    import Fuse from 'fuse.js';

    let allUsers: Promise<{
        id: number,
        firstname: string,
        lastname: string,
        email: string,
        permission: Permission[],
    }[]> = apiGet("v1/profile/foreign/shortList.php").then((res) => res.json());

    let searchTerm = "";
</script>

<div class="relative w-full z-[-1]">
        <span class="absolute inset-y-0 left-0 flex items-center pl-2">
            <span class="p-1 focus:outline-none focus:shadow-outline">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                     viewBox="0 0 256 256"><path
                        d="M229.66,218.34l-50.07-50.06a88.11,88.11,0,1,0-11.31,11.31l50.06,50.07a8,8,0,0,0,11.32-11.32ZM40,112a72,72,0,1,1,72,72A72.08,72.08,0,0,1,40,112Z"></path></svg>
            </span>
        </span>
    <input type="search" name="q"
           class="py-2 text-sm text-white bg-light_fill dark:bg-dark_fill rounded-md pl-10 focus:outline-none focus:text-gray-200 w-full"
           placeholder="Search" autocomplete="off" bind:value={searchTerm}>

</div>

<div class="flex flex-col gap-2 mt-5">
    {#await allUsers}
        <p>Loading...</p>
    {:then users}
        {#each (() => {
            if (searchTerm === "") {
                return users
            }

            const options = {
                includeScore: true,
                keys: ['firstname', 'lastname', 'email']
            }

            const fuse = new Fuse(users, options)
            const result = fuse.search(searchTerm)
            return result.map((user) => user.item)
        })() as user}
            <a href="/#/profile/{user.id}"
               class="flex items-center p-2 justify-between rounded-md bg-light_fill dark:bg-dark_fill">
                <div class="flex items-center space-x-4">
                    <img src="{backend}v1/profile/avatar/generate.php?id={user.id}" alt="Avatar"
                         class="w-12 h-12 rounded-full">
                    <div class="flex flex-col">
                        <p class="text-lg font-semibold wrap-all">{user.firstname} {user.lastname}</p>
                        <p class="text-sm text-gray-400 wrap-all">{user.email}</p>
                    </div>
                </div>

                <button class="flex items-center justify-center mr-2 w-8 h-8 rounded bg-primary hover:bg-primary_highlight focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 float-right">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                         viewBox="0 0 256 256">
                        <path d="M128,80a48,48,0,1,0,48,48A48.05,48.05,0,0,0,128,80Zm0,80a32,32,0,1,1,32-32A32,32,0,0,1,128,160Zm88-29.84q.06-2.16,0-4.32l14.92-18.64a8,8,0,0,0,1.48-7.06,107.21,107.21,0,0,0-10.88-26.25,8,8,0,0,0-6-3.93l-23.72-2.64q-1.48-1.56-3-3L186,40.54a8,8,0,0,0-3.94-6,107.71,107.71,0,0,0-26.25-10.87,8,8,0,0,0-7.06,1.49L130.16,40Q128,40,125.84,40L107.2,25.11a8,8,0,0,0-7.06-1.48A107.6,107.6,0,0,0,73.89,34.51a8,8,0,0,0-3.93,6L67.32,64.27q-1.56,1.49-3,3L40.54,70a8,8,0,0,0-6,3.94,107.71,107.71,0,0,0-10.87,26.25,8,8,0,0,0,1.49,7.06L40,125.84Q40,128,40,130.16L25.11,148.8a8,8,0,0,0-1.48,7.06,107.21,107.21,0,0,0,10.88,26.25,8,8,0,0,0,6,3.93l23.72,2.64q1.49,1.56,3,3L70,215.46a8,8,0,0,0,3.94,6,107.71,107.71,0,0,0,26.25,10.87,8,8,0,0,0,7.06-1.49L125.84,216q2.16.06,4.32,0l18.64,14.92a8,8,0,0,0,7.06,1.48,107.21,107.21,0,0,0,26.25-10.88,8,8,0,0,0,3.93-6l2.64-23.72q1.56-1.48,3-3L215.46,186a8,8,0,0,0,6-3.94,107.71,107.71,0,0,0,10.87-26.25,8,8,0,0,0-1.49-7.06Zm-16.1-6.5a73.93,73.93,0,0,1,0,8.68,8,8,0,0,0,1.74,5.48l14.19,17.73a91.57,91.57,0,0,1-6.23,15L187,173.11a8,8,0,0,0-5.1,2.64,74.11,74.11,0,0,1-6.14,6.14,8,8,0,0,0-2.64,5.1l-2.51,22.58a91.32,91.32,0,0,1-15,6.23l-17.74-14.19a8,8,0,0,0-5-1.75h-.48a73.93,73.93,0,0,1-8.68,0,8,8,0,0,0-5.48,1.74L100.45,215.8a91.57,91.57,0,0,1-15-6.23L82.89,187a8,8,0,0,0-2.64-5.1,74.11,74.11,0,0,1-6.14-6.14,8,8,0,0,0-5.1-2.64L46.43,170.6a91.32,91.32,0,0,1-6.23-15l14.19-17.74a8,8,0,0,0,1.74-5.48,73.93,73.93,0,0,1,0-8.68,8,8,0,0,0-1.74-5.48L40.2,100.45a91.57,91.57,0,0,1,6.23-15L69,82.89a8,8,0,0,0,5.1-2.64,74.11,74.11,0,0,1,6.14-6.14A8,8,0,0,0,82.89,69L85.4,46.43a91.32,91.32,0,0,1,15-6.23l17.74,14.19a8,8,0,0,0,5.48,1.74,73.93,73.93,0,0,1,8.68,0,8,8,0,0,0,5.48-1.74L155.55,40.2a91.57,91.57,0,0,1,15,6.23L173.11,69a8,8,0,0,0,2.64,5.1,74.11,74.11,0,0,1,6.14,6.14,8,8,0,0,0,5.1,2.64l22.58,2.51a91.32,91.32,0,0,1,6.23,15l-14.19,17.74A8,8,0,0,0,199.87,123.66Z"></path>
                    </svg>
                </button>
            </a>
        {/each}
    {/await}
</div>

<style>
    .wrap-all {
        word-wrap: anywhere;
    }
</style>