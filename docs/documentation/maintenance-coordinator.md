# Coordinator Model

## Table of Contents

<!-- MarkdownTOC depth=0 autolink="true" bracket="round" autoanchor="true" style="ordered" indent="   " -->
1. [Role of the Coordinator](#role-of-a-coordinator)
1. [Change Management](#change-management)
1. [Issue Management](#issue-management)
1. [Scenarios](#scenarios)
1. [What can be expected of a coordinator?](#expectations)


<!-- /MarkdownTOC -->
<a name="role-of-a-coordinator"></a>
## Role of the Coordinator

The Coordinator is not the owner of the component but much more the curator. 
The coordinator ensures the quality of contributions to the component 
and makes it possible for others to commit. The coordinator is responsible 
that the documentation is kept up to date by the contributors and that the 
guidelines of the component are met. The coordinator moderates the discussion on 
finding a vision and on the development. The coordinator further is contact 
for any sort of question that may arise about the respective component.

The motivation of the coordinator is mostly driven by the need of a 
reliable component for a certain aspect. Further the coordinator is 
probably the most attractive contractor for clients aiming to change 
aspects of the component due to the very indepth know how and the 
listing as coordinator.

<a name="change-management"></a>
## Change Management
Everybody may contribute to any aspect of the component. Such contributions 
are handed in by pull requests or some other source of data if declared so in the components guidelines. 
Pull requests on the public interface must be accepted by the JF. The coordinator gives a 
recommendation to the JF on whether to accept or decline the PR. The decision of the JF may 
be implicit if no objections to the recommendation of the coordinator is made. If no agreement 
is achieved in the JF, the Technical Board will decide upon the request.

Final implementations without further changes on the interface do not need 
formal approval by the JF. The merge of the implementation is performed 
by the coordinator or the coordinator may assign somebody to do so.

Note that the general process for feature requests must be respected. However,
this process is currently under review. The respective document will be linked as soon
as available.

<a name="issue-management"></a>
## Issue Management
Everybody is invited to make proposals on how to tackle any issue by proposing 
a respective PR. Issues of the component must be reported as bugs. The coordinators 
are responsible to assign the developer in charge on solving the bug. Due to the focus 
on code quality, a low amount of bugs should be expected.

<a name="scenarios"></a>
## Scenarios
This maintainance model is suited for components that have the potential 
to grow too large to be handled by one single developer and therefore highly 
benefit from contributions among different developers and even service providers. 
It is further important that the component is of modular structure with different 
parts following a similar scheme. It is especially suited, if some component is 
of a critical importance for many other components, since it is designed to allow 
a collaborative development of the vision for such a key aspect.

<a name="expectations"></a>
## What can be expected of a coordinator?
* The coordinator MUST moderate the discussion on finding a vision on 
the development of the component.
* The coordinator MUST give recommendations to the JF whether to accept 
or decline changes.
* The coordinator MUST accept decisions of the Technical Board on change 
requests in case of disagreement on the JF.
* The coordinator MUST review final implementation of some accepted 
interface change or organize some substitute to perform the review.
* The coordinator MAY ask for funding to perform the review.
* The coordinator MAY ask to split some interface change or implementation 
into multiple pieces to make the change easier to understand.
* The coordinator MUST ensure, that the documentation of the component is 
up to date. The coordinator MAY ask to update the documentation as a condition 
for accepting some change request.
* The coordinator MUST ensure, that the automated unit tests are kept up 
to date. The coordinator MAY ask other developers to write and update those 
tests in their own development.
* The coordinator MUST assign unassigned issues to the responsible developer.
* The coordinator SHOULD devise some guidelines concerning the processes 
around the respective component fitting it's exact needs as done so for the 
UI-Service. Such guidelines MUST be accepted by the JF.

**Please note:** The key words "MUST", "MUST NOT", "REQUIRED", "SHALL", 
"SHALL NOT", "SHOULD", "SHOULD NOT", "RECOMMENDED",  "MAY", and  "OPTIONAL" 
in this document are to be interpreted as described in [RFC 2119](https://www.ietf.org/rfc/rfc2119.txt).