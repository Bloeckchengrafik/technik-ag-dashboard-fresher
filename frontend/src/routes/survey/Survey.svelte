<script lang="ts">
    import AuthGuard from "../../lib/AuthGuard.svelte";
    import Loader from "../../lib/Loader.svelte";
    import {apiGet, apiToken, backend} from "../../api";
    import type {UserSpec} from "../../api";
    import SurveyCard from "./SurveyCard.svelte";
    import {currentTab} from "../../stores";
    import Footer from "../../lib/Footer.svelte";

    $currentTab = "quiz";

    let quizPromise: Promise<{
        [key: string]: [
            {
                id: number,
                name: string,
                category_id: number,
                category_name: string,
                done: boolean,
                score: -2 | -1 | 0 | 1 | 2,
            }
        ]
    }> = new Promise(() => {
    })

    async function reloadQuizzes() {
        quizPromise = Promise.resolve(await apiGet("v1/quiz/all.php").then(r => r.json())).then(r => {
            let quizzes = {};
            for (let quiz of r) {
                if (!quizzes[quiz.category_name]) {
                    quizzes[quiz.category_name] = [];
                }
                quizzes[quiz.category_name].push(quiz);
            }

            return quizzes;
        });
    }

    reloadQuizzes();

    let user: UserSpec;
</script>

<AuthGuard requiredPermission="doQuiz" bind:user/>

<div class="min-h-full max-w-7xl pt-2 px-1 mx-auto">
    <h1 class="text-3xl break-words mb-5">
        Umfragen
    </h1>

    {#await quizPromise}
        <div class="flex justify-center">
            <Loader/>
        </div>
    {:then quizzes}
        {#each Object.keys(quizzes) as category}
            <div class="mb-5">
                <h2 class="text-2xl break-words mb-2">
                    {category}
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    {#each quizzes[category] as quiz}
                        <SurveyCard survey={quiz}/>
                    {/each}
                </div>
            </div>
        {/each}
    {/await}

    {#if user && user.permission.includes("uploadQuiz")}
        <div class="flex justify-center">
            <button class="rounded bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4"
                    on:click={async () => {
                        let data = await apiGet("v1/quiz/download.php");
                        let blob = await data.blob();
                        let url = window.URL.createObjectURL(blob);
                        let a = document.createElement("a");
                        a.href = url;
                        a.download = "umfragen.xlsx";
                        a.target = "_blank";
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                    }}>
                Umfragen herunterladen
            </button>
            <button class="rounded bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 ml-2"
                    on:click={async () => {
                        let input = document.createElement("input");
                        input.type = "file";
                        input.accept = ".xlsx";
                        input.onchange = async () => {
                            let file = input.files[0];
                            let data = new FormData();
                            data.append("file", file);
                            await fetch(`${backend}v1/quiz/upload.php`, {
                                method: "POST",
                                body: data,
                                headers: {
                                    "Authorization": `Bearer ${$apiToken}`,
                                }
                            });
                            input.remove();
                            await reloadQuizzes();
                        }
                        input.click();
                    }}>
                Umfragen hochladen
            </button>
        </div>
    {/if}
</div>
<br />
<Footer />
