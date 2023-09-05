<script lang="ts">
    export let survey: {
        id: number,
        name: string,
        category_id: number,
        category_name: string,
        done: boolean,
        score: -2 | -1 | 0 | 1 | 2,
    }

    let scoreString = {
        '-2': 'Sehr schlecht',
        '-1': 'Schlecht',
        '0': 'Neutral',
        '1': 'Gut',
        '2': 'Sehr gut',
    }
</script>

<div class="card flex flex-row justify-between items-center">
    <h5 class="text-lg">
        <span class="font-bold">{survey.name}</span> <span class="pl-1">{#if (survey.done)}{scoreString[survey.score+'']}{/if}</span>
    </h5>
    <a href="/#/survey/{survey.id}" class="text-sm">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 256 256"><path
                d="M221.66,133.66l-72,72a8,8,0,0,1-11.32-11.32L196.69,136H40a8,8,0,0,1,0-16H196.69L138.34,61.66a8,8,0,0,1,11.32-11.32l72,72A8,8,0,0,1,221.66,133.66Z"></path></svg>
    </a>
</div>
