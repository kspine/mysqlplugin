#pragma once
#ifndef MYSQL_PLUGIN_MYSQLCONNECTOR_H_
#define MYSQL_PLUGIN_MYSQLCONNECTOR_H_

#include <poseidon/precompiled.hpp>

#include <mysql.h>

namespace  MYSQL_PLUGIN
{
	class mysqlconnector
	{
	public:
		mysqlconnector() {};

		virtual ~mysqlconnector() {};
	public:
		static mysqlconnector* GetInstance(void)
		{
			static mysqlconnector instance;
			return &instance;
		}

	public:
		bool open(const char* ip, unsigned int port, const char* scheme, const char* user, const char* pwd)
		{
			mysqlconn = mysql_init(NULL);

			if (NULL == mysqlconn)
			{
				recorderror();
				return false;
			}

			if (NULL == mysql_real_connect(mysqlconn, ip, user, pwd, scheme, port, NULL, CLIENT_MULTI_STATEMENTS))
			{
				recorderror();
				return false;
			}

			if (0 != mysql_set_character_set(mysqlconn, "utf8"))
			{
				recorderror();
				return false;

			}
			return true;
		}

		bool execute(const char*sql, std::vector<std::vector<std::string> >& data)
		{
			if (NULL == sql)
			{
				return false;
			}

			mysql_ping(mysqlconn);

			if (0 != mysql_query(mysqlconn, sql))
			{
				recorderror();
				return false;
			}

			res = mysql_store_result(mysqlconn);

			if (mysql_num_rows(res) == 0)
			{
				recorderror();
				return false;
			}

			std::uint32_t field = mysql_num_fields(res);

			MYSQL_ROW line = mysql_fetch_row(res);

			std::string temp;

			while (NULL != line)
			{
				std::vector<std::string> linedata;

				for (std::uint32_t i = 0; i < field; i++)
				{
					if (line[i])
					{
						temp = line[i];
						linedata.push_back(temp);
					}
					else
					{
						temp = "";
						linedata.push_back(temp);
					}
				}

				line = mysql_fetch_row(res);

				data.push_back(linedata);
			}

			freeres(mysqlconn, res);
			return true;
		}

		void freeres(MYSQL* mysql, MYSQL_RES *res)
		{
			do
			{
				res = mysql_store_result(mysql);
				mysql_free_result(res);
			} while (!mysql_next_result(mysql));
		}


		void close()
		{
			mysql_close(mysqlconn);
		}

		void recorderror()
		{
			errorid = mysql_errno(mysqlconn);
			error = mysql_error(mysqlconn);
		}

	public:
		MYSQL  * mysqlconn;
		MYSQL_RES * res;
		std::uint32_t errorid;
		const char* error;
	};

#define g_mysqlconnector mysqlconnector::GetInstance()
}

#endif /* MYSQL_PLUGIN_MYSQLCONNECTOR_H_ */
//call
auto mysql_ip = get_config<std::string>("mysql_server_addr", "0.0.0.0");
auto mysql_port = get_config<unsigned>("mysql_server_port", 3306);
auto mysql_user = get_config<std::string>("mysql_username", "root");
auto mysql_pwd = get_config<std::string>("mysql_password", "root");
auto mysql_scheme = get_config<std::string>("mysql_schema", "empery");
if (MYSQL_PLUGIN::g_mysqlconnector->open(mysql_ip.c_str(), mysql_port, mysql_scheme.c_str(), mysql_user.c_str(), mysql_pwd.c_str()))
{
	LOG_EMPERY_CENTER_LOG_FATAL("mysqlplugin connect mysql success", mysql_ip, mysql_port, mysql_scheme);
	char sql[1024 * 8] = { 0 };
	snprintf(sql, sizeof(sql), "call sp_procedure(format(%));", prameters¡­¡­);
	typedef std::vector<std::vector<std::string> > MULT_VECT;
	MULT_VECT data;
	if (!MYSQL_PLUGIN::g_mysqlconnector->execute(sql, data))
	{
		LOG_EMPERY_CENTER_LOG_ERROR("call sp_procedure failuer!");
		return Response();
	}
	LOG_EMPERY_CENTER_LOG_FATAL("call sp_procedure Success!");
	typedef std::vector<std::string> SUB_VECT;
	for (MULT_VECT::iterator it = data.begin(); it != data.end(); ++it)
	{
		SUB_VECT subvec = *it;
		for (SUB_VECT::iterator sit = subvec.begin(); sit != subvec.end(); ++sit)
		{
			field_value = *sit
		}
	}
}