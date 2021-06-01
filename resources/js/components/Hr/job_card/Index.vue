<template>
    <div>
        <form  @submit.prevent="onSubmit">
            <div class="panel mb-0">
                
                <div class="panel-body pb-5" >
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group has-float-label has-required select-search-group">
                                <select id="associate" v-model="fields.associate" class="allassociates no-select col-xs-12" required>
                                    <option value=""> - Select Employee - </option>
                                </select>
                                <label  for="associate"> Associate's ID </label>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-group has-float-label has-required select-search-group">
                                <input type="month" class="form-control" id="month" v-model="fields.month_year" placeholder=" Month-Year" required="required" :max="maxMonth" />
                                <label  for="month"> Month </label>
                            </div>
                        </div>
                        <div class="col-6">
                            <button type="submit"class="btn btn-primary btn-sm activityReportBtn"><i class="fa fa-save"></i> Generate</button>
                            
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="page-content content-show">
          <div class="panel w-100">
            <div class="panel-body">
              <div class="offset-1 col-10 h-min-400">
                <div id="result-data"></div>
              </div>
            </div>
          </div>
          
        </div>
    </div>
</template>
<script>

    export default {
        name: "Job-Card",
        data() {

            return {
                fields: {
                    month_year:'',
                    associate:'',
                },
                errors: {}
            }
        },
        props:['attributes'],
        computed: {
            maxMonth(){
                const current = new Date();
                const month = ((current.getMonth()+1) < 10 ? '0' : '') + (current.getMonth()+1);
                return current.getFullYear()+'-'+month;
            }

        },
        methods:{
            onSubmit(){
                
                axios.get('/hr/reports/job-card-report', { params: this.fields }).then( response => {
                    if(response !== 'error'){
                        console.log('hi')  
                    }
                } ).catch((error) => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {}; 
                    }
                });
            }
        },
        mounted(){
            this.fields.associate = this.attributes.associate;
            this.fields.month_year = this.attributes.month_year;
        }
    }
</script>