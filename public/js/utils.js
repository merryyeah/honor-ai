function setAxios(){
    if(!window.axios) return;
    window.baseURL =  'http://0311.world/',
    window.http  = axios.create({
        baseURL: baseURL,
        timeout: 30000,
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        }
      });
    // Add a request interceptor
    http.interceptors.request.use(function (config) {
        // Do something before request is sent
        let formData = new FormData();
        Object.keys(config.data).forEach(key=>{
            formData.append(key, config.data[key]);
        })
        config.data = formData
        return config;
      }, function (error) {
        // Do something with request error
        return Promise.reject(error);
      });
    
    // Add a response interceptor
    http.interceptors.response.use(function (response) {
        // Do something with response data
        return response.data;
      }, function (error) {
        // Do something with response error
        return Promise.reject(error);
      });
    
}

setAxios()



function appGetTime(serverTime) {
  var date = new Date(serverTime*1000);   
  var year = date.getFullYear();    //获取当前年份   
  var mon = date.getMonth()+1;      //获取当前月份   
  var da = date.getDate();          //获取当前日   
  var day = date.getDay();          //获取当前星期几   
  var h = date.getHours();          //获取小时   
  var m = date.getMinutes();        //获取分钟   
  var s = date.getSeconds();        //获取秒   
  var d = document.getElementById('Date');    
  let add0 = function(num){
    if(num<10){
      return `0${num}`
    }
    return num
  }
   let days = ['日','一','二','三','四','五','六']
  return year+'.'+mon+'.'+da+' '+'星期'+days[day]+' '+add0(h)+':'+add0(m)+':'+add0(s); 
}
function appGetTimeList(start,end,interval_times) {
  let temStart = start
  let list = [start]
  while (temStart < end){
    temStart = temStart + interval_times
  
    list.push(temStart)
  }
  return list
}