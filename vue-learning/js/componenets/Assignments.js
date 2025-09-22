import AssignmentList from "./AssignmentList.js";

export default {

        components: {AssignmentList},

        template: 
        `
            <assignment-list :assignments="filters.inProgress" title="In progress"></assignment-list>

            <assignment-list :assignments="filters.completed" title="Completed"></assignment-list>
        `,
         data() {
                return {
                    assignments: [
                        { name: 'Finish project', complete: false, id: 1 },
                        { name: 'Work out', complete: false, id: 2 },
                        { name: 'Make dinner', complete: false, id: 3 }
                    ]
                };
            },

            computed: {
                filters(){
                    return {
                        inProgress: this.assignments.filter(assignment => !assignment.complete),
                        completed: this.assignments.filter(assignment => assignment.complete),
                    };
                }
        }

    }