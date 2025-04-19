### Phase 1: Auto Exam Creation from PDF via Google Cloud Vision API
Objective: Automatically create a structured exam from a teacher-uploaded PDF containing MCQs using OCR via Google Cloud Vision API.

the pdf contains the question number, questions, options and the correct answers.

OCR Provider:
Google Cloud Vision API

{
  "type": "service_account",
  "project_id": "lively-oxide-453105-k9",
  "private_key_id": "3c99f8bc80079ce3ebd1a8facae8838886455bdb",
  "private_key": "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCdMoA/duT0w08M\nJBacZCskY5x9VlY8LD9gjJDE/w5elvPslpUz+fq4V86nIWGbIv0itox6leb0ZSso\n+rWEp+QDxQIjfz5LsoWrCIxzf2NeQpxM2wtWyuvMoifnommH3cXVn6h2wKZDxpmv\nazYPQv+tlN+9gUXJNmQZVrTVQPbpP1JiZKyXUgmx6LgvymiHNxIKFAbEc6QwJsIk\nMVpUtdQMpy1PNCI0MnG6LAooJvZIvVh1SchJTH93q32vqtKYeQnw4t8rqlmhyWYX\nn17fOlhqSAMkXBA0By5pQAJUgLoq8qQuBaZyyrl650l3NAXhafwVADTCFV5O1xrq\nCbnvhsDfAgMBAAECggEARERDN8x9X+JVuNMPUrmZlsL/mdrdtmIM3/QLhtGxPtjV\nc6BtiVX9UQMpBqHTjpjiT6nxVec1lls9JB3EqKh0uEJdTGcc+ai8resXe60NzUim\nMiZKRVX4kzS0mb6Jj+x61uvwMCo3ymU8JtcrcfNJr+tgQx8Z3GkXjX1/KMF+uQoN\ni1v3ukLSCZUB3EySHa2+NghEJQli50AByr0BKMSa7//FR3sweeSjCMSFQWjLvHJ7\nFA5rpQTqKwDyTlG2vvvNvymp+Y5GJ3Xhx5yfKQ29VSfgKSyHVtYo4qpgrtCDepND\n2kupMmrAHaTMY7Puhxpz2kXU3+7AXtqKY6G8irJBsQKBgQDMZcgw63bPAkogTnoZ\n/66thO6Ji2Y7ArNTnFyB1iW0LtMh79C9KzsRS+YpsZYqcKgzParLzQeKQClA25sj\nsuoQJIYwX/rNLcTjD0XcrX2UC10KnHMWQP2PqoMBIZlwCGQJonXLyFWxBTmz57Vx\nAbBkoDIyppSE/De66gBSEOP+aQKBgQDE4il791FIaI0s6WDMptqULSfFsOKGO+Ea\nw79UxKJi8axiTgPhMGOsnywpGwHndLBBYAMVLA7aSYiBEbqsTxbRL+4uTa9RQIWz\nlPhd/kd7GbE4ueD3Zos6aPiJs6AzihIzxmIQ/lfCYL9+FGXxW5eQmIH9/W2dicCU\n3F8SAHLsBwKBgDhYLnFd12iFRw3U1E/qbaqjpGYCKXJG8kwwJEgeUlJzPfxy8WXU\nTvobpB0GOVEFsTg+3aBEqrlvqm+YmhGjNmawytT3AFojLc0x3p5QrPdskn9kVU1j\nKQK7jFV5f6Ski38ka7h7RzP/LsLrMtcuEgQLLQtNZE+sf2hlLSVyRodRAoGAPqty\n4wflcrP9BCfBbUNLXlFFULMuV+YlkDxw4c25lt8wrRYCPCMIB0Gfb/It1/wXwqeK\nM6oTjD5N2i/HiOOf8rRcD5cg0C9Gn9QyftDa9f9GnzTjvDyC4vPY8RQhUWaIxrxG\nKPyc+L2NFtftXzd1dkxlTHDn/HaFu5yuEm/cJO8CgYEAoFR045f0rXwwzyYUuM4j\n3pjPU0ZLa3ds0RRHEHAOCYxtS85kMGM7QQIbpGlYTszQGLW4MjbY5xiQgM8gVa8S\nYOqit2PVE3Bc38cAfctAcWpcCQFpZwPeJCzR3mQ0aEwVBM5b89Xz0kb/guFzFjmL\n1FjgAQ0d1ZXsFcEdi23cRzc=\n-----END PRIVATE KEY-----\n",
  "client_email": "matchmaking@lively-oxide-453105-k9.iam.gserviceaccount.com",
  "client_id": "101410309566437621466",
  "auth_uri": "https://accounts.google.com/o/oauth2/auth",
  "token_uri": "https://oauth2.googleapis.com/token",
  "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
  "client_x509_cert_url": "https://www.googleapis.com/robot/v1/metadata/x509/matchmaking%40lively-oxide-453105-k9.iam.gserviceaccount.com",
  "universe_domain": "googleapis.com"
}

Key Actions:

Add an "Upload" button beside the +add button in the Add exams tab in exam portal of the Teacher Dashboard where the teacher creates a new test after entering the start time, the submission time and the number of questions. The  teacher can wither enter them manually or click the upload button

On click, prompt the teacher to upload a PDF file that contains MCQs. 

Use the Google Cloud Vision API to extract text from the uploaded PDF.

Parse and structure the extracted text in the following format:

Question <number>: <Question text>  
<Option A>  
<Option B>  
<Option C>  
<Option D>
Correct answer: <One of the options>
(Use intelligent parsing and cleanup to ensure proper formatting even if the OCR output is noisy.)

Take the first x questions where x is the number of questions selected by the teacher and automatically fill the question name, options and the correct answer on the addqp.php page(with the help of the structured data extracted from the pdf)

Allow the teacher to review the auto-structured exam before publishing. 

Insert the questions into the database just like a manually created exam.

The teacher reviews the exam and then publishes it as she would manually which then gets displayed on the students exam portal.
