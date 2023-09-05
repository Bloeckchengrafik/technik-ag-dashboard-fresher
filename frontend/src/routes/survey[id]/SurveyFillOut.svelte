<script lang="ts">
    import AuthGuard from "../../lib/AuthGuard.svelte";
    import {apiGet, apiPost} from "../../api";
    import {currentTab} from "../../stores";
    import SubmitBtn from "../../lib/Forms/SubmitBtn.svelte";

    export let params: { id: string };
    let id = parseInt(params.id);

    $currentTab = "quiz";

    let dataPromise: Promise<{
        quiz: {
            id: number,
            name: string,
            category_id: number,
            category_name: string,
        },
        questions: [{
            id: number,
            quiz_id: number,
            question: string,
        }]
    }> = apiGet("v1/quiz/questions.php?id=" + id).then(r => r.json());

    let submittedData: {
        quiz_id: number,
        answers: [
            {
                question_id: number,
                answer: number,
            }?
        ]
    } = {
        quiz_id: id,
        answers: []
    };
</script>
<AuthGuard requiredPermission="doQuiz"/>

<div class="min-h-full max-w-7xl pt-2 px-1 mx-auto">
    {#await dataPromise}
        <p class="text-3xl break-words mb-5">
            Loading...
        </p>
    {:then data}
        <h1 class="text-3xl break-words mb-5">
            Umfrage <b>{data.quiz.name}</b> ausfüllen
        </h1>

        <!-- the first is as small as possible in grid -->
        <div class="grid grid-cols-2 gap-5 mb-5">
            {#each data.questions as question}
                <p class="text-2xl break-words mb-2">
                    {question.question}
                </p>

                <div class="flex flex-row mb-5 gap-5">
                    <div class="flex-1 flex flex-row gap-2 items-center">
                        👎 <input type="range" value="0" min="-2" max="2" step="1"
                                 class="w-full h-2 bg-dark_fill rounded-lg appearance-none cursor-pointer"
                                 on:change={e => {
                                        let myPosition = -1
                                        // Search if question is already in array
                                        for (let i = 0; i < submittedData.answers.length; i++) {
                                            if (submittedData.answers[i]?.question_id === question.id) {
                                                myPosition = i;
                                                break;
                                            }
                                        }

                                        if (myPosition === -1) {
                                            submittedData.answers.push({
                                                answer: parseInt(e.target.value),
                                                question_id: question.id
                                            });
                                        } else {
                                            submittedData.answers[myPosition] = {
                                                answer: parseInt(e.target.value),
                                                question_id: question.id
                                            };
                                        }
                                    }}/> 👍
                    </div>
                </div>
            {/each}
        </div>

        <SubmitBtn name="Absenden" on:click={async () => {
            // Fill out remaining questions with 0
            for (let i = 0; i < data.questions.length; i++) {
                if (submittedData.answers[i] === undefined) {
                    submittedData.answers[i] = {
                        answer: 0,
                        question_id: data.questions[i].id
                    };
                }
            }

            await apiPost("v1/quiz/submit.php", submittedData);
            window.location.href = "/#/survey";
        }}/>
    {/await}
</div>