import { ref, reactive, watch } from 'vue';

const DAYS_KEY = 'vds.myventure.edit.days.v1';
const ASSIGN_KEY = 'vds.myventure.edit.assign.v1';

const days = ref([]);
const assignment = reactive({});

// init once
(function init() {
    try {
        const d = JSON.parse(localStorage.getItem(DAYS_KEY) || '[]');
        days.value = Array.isArray(d) ? d : [];
    } catch { days.value = []; }

    try {
        const a = JSON.parse(localStorage.getItem(ASSIGN_KEY) || '{}');
        if (a && typeof a === 'object') Object.assign(assignment, a);
    } catch { }
})();

watch(days, v => localStorage.setItem(DAYS_KEY, JSON.stringify(v)), { deep: true });
watch(assignment, v => localStorage.setItem(ASSIGN_KEY, JSON.stringify(v)), { deep: true });

function renumberDays(preserveCustom = true) {
    // keep order; just normalize labels Day 1..N when default-like
    let n = 1;
    days.value = days.value.map(d => {
        const isDefault = /^Day \d+$/.test(d.title || '');
        return { ...d, title: (preserveCustom && !isDefault) ? d.title : `Day ${n++}` };
    });
}

function addDay(title) {
    const nextId = (days.value.length ? Math.max(...days.value.map(d => d.id)) : 0) + 1;
    days.value.push({ id: nextId, title: title || `Day ${days.value.length + 1}` });
    renumberDays(true);
}

function deleteDay(dayId) {
    //   if (days.value.length <= 1) { alert('You must have at least one day.'); return; }
    const remaining = days.value.filter(d => d.id !== dayId);
    const fallbackId = remaining[0]?.id;
    Object.keys(assignment).forEach(k => {
        if (assignment[k] === dayId) assignment[k] = fallbackId;
    });
    days.value = remaining;
    renumberDays(true);
}

function renameDay(dayId, newTitle) {
    const d = days.value.find(x => x.id === dayId);
    if (d && newTitle && newTitle.trim()) d.title = newTitle.trim();
}

function latestDayId() {
    if (!days.value.length) addDay('Day 1');   // create Day 1 on first-ever add
    return days.value[days.value.length - 1].id;
}

function removeAssignmentForKey(k) {
    if (k in assignment) delete assignment[k];
}

export function useVenturePlan() {
    return { days, assignment, addDay, deleteDay, renameDay, renumberDays, latestDayId, removeAssignmentForKey };
}
