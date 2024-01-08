### CSV import, filtering and export tool

This readme assumes you have docker installed.

#### Installation:
1) Run `git clone https://github.com/cernei/rexit-test-task.git`
2) Run `cd rexit-test-task/.docker`
3) Run `docker compose up` 

Configuration assumes you have 8080 port available, otherwise you have to manually map container's port to available one on your machine in `docker.compose.yml` file

4) Wait until all containers are built and started. Look at the logs.
5) Navigate to `http://localhost:8080/frontend-app.html`