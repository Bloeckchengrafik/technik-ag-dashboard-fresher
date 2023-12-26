<script lang="ts">
    import type { FullEvent, Shift as ShiftSpec } from "./fullEvent";
    import type { UserSpec } from "../../api";
    import {createEvents, type EventAttributes } from "ics";
    export let event: FullEvent;
    export let user: UserSpec;

    function format_date(
        date: string
    ): [number, number, number, number, number] {
        let d = new Date(date.replace(" ", "T"));
        return [
            d.getFullYear(),
            d.getMonth() + 1,
            d.getDate(),
            d.getHours(),
            d.getMinutes(),
        ];
    }

    async function generateCalendar(): Promise<string> {
        let events: EventAttributes[] = [];
        for (let shift of event.shifts) {
            if (
                shift.users
                    .map((u) => {
                        return u.id === user.id;
                    })
                    .includes(true)
            ) {
                events.push({
                    productId: "goetec",
                    uid:
                        shift.event_id +
                        "+" +
                        shift.id +
                        "@goetec.goethe-projektserver.de",
                    start: format_date(shift.from_time),
                    end: format_date(shift.to_time),
                    busyStatus: "BUSY",
                    title: shift.name + " bei " + event.title + " @ GOETEC",
                    categories: ["GOETEC"],
                    attendees: shift.users.map((user) => {
                        return {
                            name: user.firstname + user.lastname,
                            partstat: "ACCEPTED"
                        }
                    }),
                    location: event.room,
                    organizer: {
                        name: event.organizer.firstname + event.organizer.lastname,                        
                    },
                    status: "CONFIRMED"
                });
            }
        }
        return await new Promise((res) => {
            let data = createEvents(events)
            res(data.value)
        });
    }

    async function exportShifts() {
        let cal = await generateCalendar();
        let blob = new Blob([cal], { type: "text/calendar;charset=utf-8" });
        let url = window.URL.createObjectURL(blob);
        let a = document.createElement("a");
        a.href = url;
        a.download = "shifts.ics";
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }
</script>

<svg
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 256 256"
    width="30"
    height="30"
    on:click={exportShifts}
    ><rect width="256" height="256" fill="none" /><path
        d="M176,104h24a8,8,0,0,1,8,8v96a8,8,0,0,1-8,8H56a8,8,0,0,1-8-8V112a8,8,0,0,1,8-8H80"
        fill="none"
        stroke="currentColor"
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="16"
    /><polyline
        points="88 64 128 24 168 64"
        fill="none"
        stroke="currentColor"
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="16"
    /><line
        x1="128"
        y1="24"
        x2="128"
        y2="136"
        fill="none"
        stroke="currentColor"
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="16"
    /></svg
>
