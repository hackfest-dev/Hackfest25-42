#Anti-cheat system for tracking cheating during an MCQ

#The front end(MCQ) part has already been done using PHP, MySQL(database fully ready) and Apache servers. Go through the project once and take appropriate steps.

#The Anti-cheat system has to be integrated to the MCQ

#Use appropriate tech stack for each phase

#Development roadmap

### Phase 1: Tab Switching Detection

-Objective: Detect switching to other browser tabs using the Visibility API.

-Key Actions:

-Track visibilitychange events on the exam page

-Log each tab switch attempt in the database

-Apply escalating penalties (3 → 5 → 8 points per violation)

-Penalty Structure:

Switches Deduction Total Loss
1st -3 -3
3rd -8 -16
5th+ -15 -43+

### Phase 2: Window Focus/Blur Tracking

-Objective: Identify when the exam window loses focus (minimizing/clicking outside).

-Key Actions:

-Monitor blur and focus browser events

-Distinguish between accidental and repeated triggers

-Apply progressive penalties (2 → 4 → 6 points)

-Penalty Structure:

Blurs Deduction Total Loss
1st -2 -2
3rd -6 -12
4th+ -8 -20+

### Phase 3: Combined Behavior Analysis

-Objective: Detect coordinated cheating patterns.

-Key Actions:

-Identify rapid tab + window switching within 2 seconds

-Apply heavy penalties (10 → 15 → 20 points)

-Mandate manual review for violations

-Penalty Structure:

Occurrences Deduction Total Loss
1st -10 -10
2nd -15 -25
3rd+ -20 -45+

### Phase 4: Integrity Score Classification

-Objective: Final evaluation of exam legitimacy.

-Scoring Logic:

-Starts at 100 points

-Subtracts cumulative penalties

-Classifies results into three tiers

-Classification Standards:

-"Good" Score Range (75–100)\*\*:  
 Most students will fall into this range, ensuring that the system isn't too strict.
Goal: Let students who behave honestly pass with minimal interference.

- "At-Risk" Score Range (50–74):  
   Only a small percentage of students should fall into this group. This group shows some suspicious behaviors, but there’s no definitive proof of cheating.
  Goal: Admins review these cases, possibly sparking further investigation.

- "Cheating Suspicion" Score Range (0–49)\*\*:  
   A very small percentage should fall into this category. These are students who either make repeated, serious mistakes or show clear signs of trying to evade the monitoring system.
  Goal: Ensure these students face consequences, either by disqualification or a detailed manual review.
