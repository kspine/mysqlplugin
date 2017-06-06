# mysqlplugin
mysqlplugin
  
  process date to timestamp & get current date
  // get date timestamp
       static time_t get_date_timestamp(const char*  date,const char* format = "%Y%m%d") {
	  struct tm time_st = {0};  
	  if (!date) {
	      return time(NULL);
	  }
	  strptime(date,format, &time_st) ;
	  time_t tt=mktime(&time_st);
	  return tt;
      }
      
       // get_current_date
       static std::uint64_t get_current_date()
       {
          time_t rawtime;
          tm * timeinfo;
          char buffer[10];
     
          time (&rawtime);
          timeinfo = localtime( &rawtime );
          strftime(buffer, sizeof(buffer), "%Y%m%d", timeinfo);
         
          return static_cast<std::uint64_t>(get_date_timestamp(buffer));
       }
       
