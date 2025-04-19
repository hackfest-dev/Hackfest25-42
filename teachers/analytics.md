### Project: Analytics
   Add another column in the results tab of the teacher's dashboard called analytics. for each exam add a view analytics button which displays all the analytics

   We need to store each student’s selected answers in a structured way in the database so that we can later generate analytics using the OpenAI API.

   Please update our backend code and database schema to:

   1. Store each student's selected option for every question in an exam.
   2. Include whether their selected option was correct.
   4. Include creation of new tables if necessary (`student_answers`, `questions`, `options` etc.)
   5. Modify or add any insert logic needed to record the data during exam submission.
   6. Also prepare an endpoint that fetches structured analytics-ready data per exam — including:
      - Question text
      - All options with counts of how many students chose each
      - Correct option
      - Total responses per question
      - Number of correct responses per question

   The data fetched by this endpoint will be passed to the OpenAI API to generate:
   - Natural language summaries of the exam
   - Insights about student performance
   - Recommendations on what to re-teach
   - Graph type suggestions

   Make the API endpoint is clean and return a well-structured JSON that the OpenAI API can understand.

   OpenAPI credentials:
   $openai_base_url = "https://ai-graphitestorm8466ai385706727975.openai.azure.com/";
   $openai_api_key = "Ax80ppCsRf3baI69t4Ww7WdIgE2ywqwmoxVQk8WXiX5rN2Q6bYv0JQQJ99BCACHYHv6XJ3w3AAAAACOGTC2b";
   $openai_deployment = "gpt-4o"; 
   $openai_api_version = "2023-07-01-preview";
