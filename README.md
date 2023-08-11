
Assignment: 

Create a parser that extracts the required data from a specific webpage and indexes it into an ElasticSearch index.

From the page //foxentry.com/cs/cenik-api, retrieve and index the data as follows:
• Obtain information about all offered services (1 row = 1 service)
• For each service, retrieve its name, description (displayed on hovering over the ℹ️ icon), and prices.
• Create an ElasticSearch index and index each service as a separate document.

Upload the source code to a GIT repository and remember that the code should be readable for other developers.

About APP: 
 - Application is made in PHP framework Laravel
 - Application parse services from URL and index them into ElasticSearch
 - As web server is use NGINX
 - Application is packed in Docker

Requirenments: Docker

How to run: 
In root folder type into terminal:  - docker-compose up -d --build
                                    - go on http://localhost:8080/parse-services
                                    - additionaly you can setup Kibana to check ES index or any other service
