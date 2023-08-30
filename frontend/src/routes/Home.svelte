<script lang="ts">
    import desk from '../assets/scroller/desk.jpg';
    import Hero from "./Hero.svelte";
    import Banner from "./Banner.svelte";
    import Footer from "../lib/Footer.svelte";
    import {currentTab} from "../stores";

    $currentTab = "home";
</script>
<div class="h-full">
    <img src={desk} alt="desk"
         class="absolute top-0 left-0 w-full h-full -z-10 object-cover opacity-90"/>
    <Hero/>
    <Banner/>
</div>
<div class="h-32"></div>
<div class="max-w-7xl mx-auto">
    <h1 class="text-4xl font-bold mt-10">H&auml;ufig gestellte Fragen</h1>
    <div class="mt-10">
        <h2 class="text-2xl font-bold">Was muss ich vor der Buchung der Technik beachten?</h2>
        <p class="mt-2" >
            Bevor Sie die Technik für eine Veranstaltung buchen, müssen Sie die Veranstaltung erst von der Schulleitung genehmigen und den Raum reservieren lassen.
        </p>
    </div>
    <div class="mt-10">
        <h2 class="text-2xl font-bold">Wie geht es weiter, nachdem ich die Veranstaltung angemeldet habe?</h2>
        <p class="mt-2" >
            Nach der Anmeldung erhalten Sie von uns eine Email, über die Sie die Veranstaltung über einen Link aktivieren müssen. Später können Sie die Veranstaltung über diesen Link jederzeit einsehen und Änderungen an Ihren Angaben machen. Sie sehen auf dieser Seite auch, wann die Technik auf und abgebaut werden soll und welche Techniker sich für Ihre Veranstaltung engagieren.
        </p>
    </div>
    <div class="mt-10">
        <h2 class="text-2xl font-bold">Wer hat die Aufsicht über die Techniker?</h2>
        <p class="mt-2" >
            Die Aufsicht über die Techniker hat - sofern keine anderen Absprachen mit der AG-Leitung bestehen - die Lehrkraft, die die Veranstaltung anmeldet und verantwortet. Dies betrifft insbesondere auch den Auf- und Abbau der Technik.
        </p>
    </div>
</div>

<div class="h-10"></div>
<Footer />