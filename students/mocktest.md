### Phase 1: Mock Exam Generation via Azure OpenAI
Objective: Automatically generate two MCQ-based mock tests for each exam created by a teacher.

OpenAI creds:

gpt-4o (version:2024-11-20)
endpoint : https://ai-graphitestorm8466ai385706727975.openai.azure/
api key :  Ax80ppCsRf3baI69t4Ww7WdIgE2ywqwmoxVQk8WXiX5rN2Q6bYv0JQQJ99BCACHYHv6XJ3w3AAAAACOGTC2b

Key Actions:
   

   Create a new "Mock Exams" tab in the student dashboard.

   As soon as a teacher uploads a new exam read the exam name and description from the field of add exam in exam.php, trigger the Azure OpenAI API.

   Use the exam title and description to generate two mock exams, each containing 5 MCQs with 4 options.

   Save mock exam questions in the database for the student to attempt.

   Make all changes to the db_eval.sql file and give me a php executable file for making the changes to the database via the browser.

   Do not make any changes to the teacher dashboard. I want the mock exams created as soon as the teacher creates a normal exam. You can add a loading screen on the student's mock exam page to let him know that thhe mock exams are being created.

   The student should recieve

      Exam Score

      Integrity Score

Functional Requirements:

   Mimic the behaviour of the normal exam exactly

   Inherit all features from the normal exam(read through the project and add all features)

   Exclude NFT certificate generation for mock tests.

   Ensure database cleanup post-exam submission to avoid clutter or misuse.